<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>PDF Page Remover</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.min.js"></script>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        #pageList {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .pageItem {
            width: 100px;
            height: 140px;
            position: relative;
            cursor: pointer;
            border: 1px solid #ccc;
        }

        .pageItem canvas {
            width: 100%;
            height: 100%;
            filter: none;
            transition: filter 0.3s;
        }

        .deleteOverlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.4);
            color: white;
            font-weight: bold;
            font-size: 16px;
            align-items: center;
            justify-content: center;
            z-index: 10;
            backdrop-filter: blur(2px);
        }

        .pageItem:hover .deleteOverlay {
            display: flex;
        }

        .pageItem:hover canvas {
            filter: blur(2px);
        }

        #downloadBtn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>

<body>

    <h2>Click on a page to delete it</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />
    <div id="pageList"></div>
    <button id="downloadBtn" disabled>Download PDF</button>

    <script>
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.worker.min.js';

        let originalPdfBytes = null;
        let currentPdfBlobUrl = null;
        let fileObject = null;

        const pdfFileInput = document.getElementById('pdfFile');
        const pageList = document.getElementById('pageList');
        const downloadBtn = document.getElementById('downloadBtn');

        pdfFileInput.addEventListener('change', async (e) => {
            fileObject = e.target.files[0];
            if (!fileObject || fileObject.type !== 'application/pdf') {
                alert('Please select a valid PDF.');
                return;
            }

            originalPdfBytes = await fileObject.arrayBuffer();
            await renderPdf(originalPdfBytes);
        });

        async function renderPdf(pdfBytes) {
            const pdf = await pdfjsLib.getDocument({
                data: pdfBytes
            }).promise;
            pageList.innerHTML = '';

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({
                    scale: 0.2
                });

                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');
                await page.render({
                    canvasContext: ctx,
                    viewport
                }).promise;

                const pageItem = document.createElement('div');
                pageItem.className = 'pageItem';
                pageItem.setAttribute('data-page-number', i);

                const overlay = document.createElement('div');
                overlay.className = 'deleteOverlay';
                overlay.innerText = 'DELETE';

                pageItem.appendChild(canvas);
                pageItem.appendChild(overlay);
                pageList.appendChild(pageItem);

                pageItem.addEventListener('click', () => {
                    const pageNumber = parseInt(pageItem.getAttribute('data-page-number'));
                    removePageFromServer(fileObject, pageNumber);
                });
            }

            downloadBtn.disabled = false;
        }

        async function removePageFromServer(file, pageToRemove) {
            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('page', pageToRemove);
                formData.append('apiKey', 'asd'); // Replace with your actual API key
                formData.append('platform', 'frontend');

                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/removePage', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errText = await response.text();
                    alert('Error removing page: ' + errText);
                    return;
                }

                const newPdfBlob = await response.blob();
                if (currentPdfBlobUrl) URL.revokeObjectURL(currentPdfBlobUrl);
                currentPdfBlobUrl = URL.createObjectURL(newPdfBlob);

                fileObject = new File([newPdfBlob], "updated.pdf", {
                    type: "application/pdf"
                });
                const newBytes = await newPdfBlob.arrayBuffer();
                await renderPdf(newBytes);

            } catch (err) {
                alert('Failed to remove page: ' + err.message);
            }
        }

        downloadBtn.addEventListener('click', () => {
            if (!currentPdfBlobUrl) {
                alert('No updated PDF to download.');
                return;
            }
            const a = document.createElement('a');
            a.href = currentPdfBlobUrl;
            a.download = 'pagedeleted.pdf';
            a.click();
        });
    </script>
</body>

</html>