<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>PDF Rearranger</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <style>
        #pageList {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .pageItem {
            border: 1px solid #ccc;
            cursor: grab;
            width: 100px;
            height: 140px;
            position: relative;
        }

        canvas {
            width: 100%;
            height: 100%;
        }

        #downloadBtn {
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <h2>Drag pages to rearrange order</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />
    <div id="pageList"></div>
    <button id="downloadBtn" disabled>Download PDF</button>

    <script>
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.worker.min.js';

        let originalPdfBytes = null;
        let pagesOrder = [];
        let currentPdfBlobUrl = null;

        const pageList = document.getElementById('pageList');
        const pdfFileInput = document.getElementById('pdfFile');
        const downloadBtn = document.getElementById('downloadBtn');

        pdfFileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file || file.type !== 'application/pdf') {
                alert('Please select a PDF file.');
                return;
            }

            originalPdfBytes = await file.arrayBuffer();
            pagesOrder = [];

            const pdf = await pdfjsLib.getDocument({
                data: originalPdfBytes
            }).promise;
            pageList.innerHTML = '';

            for (let i = 1; i <= pdf.numPages; i++) {
                pagesOrder.push(i);
                const pageItem = document.createElement('div');
                pageItem.className = 'pageItem';
                pageItem.setAttribute('data-page-number', i);

                const canvas = document.createElement('canvas');
                pageItem.appendChild(canvas);
                pageList.appendChild(pageItem);

                const page = await pdf.getPage(i);
                const viewport = page.getViewport({
                    scale: 0.2
                });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');
                await page.render({
                    canvasContext: ctx,
                    viewport
                }).promise;
            }

            downloadBtn.disabled = false;
            currentPdfBlobUrl = null;
            setupSortable();
        });

        function setupSortable() {
            Sortable.create(pageList, {
                animation: 150,
                onEnd: async () => {
                    pagesOrder = Array.from(pageList.children).map(div => Number(div.getAttribute('data-page-number')));
                    console.log('New order:', pagesOrder);
                    await rearrangePdf(pagesOrder);
                }
            });
        }

        async function rearrangePdf(newOrder) {
            try {
                const formData = new FormData();


                formData.append('file', pdfFileInput.files[0]);


                newOrder.forEach(pageNum => formData.append('order', pageNum));

                formData.append('apiKey', 'asd'); // potom api kluce 
                formData.append('platform', 'frontend');

                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/rearrange', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    alert('Error rearranging PDF: ' + text);
                    return;
                }

                const pdfBlob = await response.blob();

                if (currentPdfBlobUrl) URL.revokeObjectURL(currentPdfBlobUrl);
                currentPdfBlobUrl = URL.createObjectURL(pdfBlob);

            } catch (err) {
                alert('Failed to rearrange PDF: ' + err.message);
            }
        }


        downloadBtn.addEventListener('click', () => {
            if (!currentPdfBlobUrl) {
                alert('No rearranged PDF available yet.');
                return;
            }
            const a = document.createElement('a');
            a.href = currentPdfBlobUrl;
            a.download = 'rearranged.pdf';
            a.click();
        });
    </script>
</body>

</html>