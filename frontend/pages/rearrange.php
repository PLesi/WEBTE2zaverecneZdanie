<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="operations.rearrange">Preskupiť stránky</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation bar -->
    <?php include 'navbarPDFoperations.php'; ?>

    <h2 data-i18n="rearrange.instruction">Presuňte stránky pre zmenu poradia</h2>
    <input type="file" id="pdfFile" accept="application/pdf" data-i18n-placeholder="rearrange.placeholder_upload" />
    <div id="pageList"></div>
    <button id="downloadBtn" disabled data-i18n="rearrange.download_button">Stiahnuť PDF</button>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.min.js"></script>
    <!-- PDF.js -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS with defer -->
    <script defer src="../assets/js/i18n.js"></script>
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
                alert(i18next.t('rearrange.error_invalid_file'));
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
                    alert(i18next.t('rearrange.error_rearrange', { error: text }));
                    return;
                }

                const pdfBlob = await response.blob();
                if (currentPdfBlobUrl) URL.revokeObjectURL(currentPdfBlobUrl);
                currentPdfBlobUrl = URL.createObjectURL(pdfBlob);

            } catch (err) {
                alert(i18next.t('rearrange.error_failed', { error: err.message }));
            }
        }

        downloadBtn.addEventListener('click', () => {
            if (!currentPdfBlobUrl) {
                alert(i18next.t('rearrange.error_no_pdf'));
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