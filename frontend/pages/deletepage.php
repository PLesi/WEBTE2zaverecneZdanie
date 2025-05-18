<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PDF Page Remover</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation bar -->
    <?php include 'navbar.php'; ?>

    <h2>Click on a page to delete it</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />
    <div id="pageList"></div>
    <button id="downloadBtn" disabled>Download PDF</button>

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>
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