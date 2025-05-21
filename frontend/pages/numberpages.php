<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="operations.number">Číslovať stránky</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">

</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <h2 data-i18n="number.title">Číslovať stránky</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />

    <div class="controls">
        <label for="positionSelect" data-i18n="number.label_position">Pozícia číslovania:</label>
        <select id="positionSelect" default="bottomRight">
            <option value="bottomRight" data-i18n="number.option_bottomRight">Spodná pravá</option>
            <option value="bottomLeft" data-i18n="number.option_bottomLeft">Spodná ľavá</option>
            <option value="topLeft" data-i18n="number.option_topLeft">Horná ľavá</option>
            <option value="topRight" data-i18n="number.option_topRight">Horná pravá</option>
        </select>
        <input type="number" id="fontSize" placeholder="Veľkosť písma" min="8" max="42" value="13" />
        <button id="numberBtn" data-i18n="number.number_button">Číslovať stránky</button>
        <button id="downloadBtn" style="display:none" data-i18n="number.download_button">Stiahnuť číslované PDF</button>
    </div>

    <div id="pageList"></div>

    <!-- Modal for big page preview -->
    <div id="modalOverlay">
        <div id="modalContent">
            <button id="modalCloseBtn" title="Close">&times;</button>
            <canvas id="modalCanvas"></canvas>
        </div>
    </div>
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

        let uploadedFile = null;
        let numberedPdfBlobUrl = null;
        let fontSize = document.getElementById('fontSize').value;

        let currentPdf = null; // pdfjsLib PDFDocumentProxy for currently rendered PDF

        document.getElementById('fontSize').addEventListener('input', (e) => {
            fontSize = e.target.value;
        });
        const pdfFileInput = document.getElementById('pdfFile');
        const positionSelect = document.getElementById('positionSelect');
        const numberBtn = document.getElementById('numberBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const pageList = document.getElementById('pageList');

        const modalOverlay = document.getElementById('modalOverlay');
        const modalCanvas = document.getElementById('modalCanvas');
        const modalCloseBtn = document.getElementById('modalCloseBtn');

        pdfFileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file || file.type !== 'application/pdf') {
                alert(i18next.t('number.error_invalid_file'));
                return;
            }
            uploadedFile = file;
            const buffer = await file.arrayBuffer();
            await renderPdf(buffer);
        });

        numberBtn.addEventListener('click', async () => {
            if (!uploadedFile) {
                alert(i18next.t('number.error_no_file'));
                return;
            }

            const position = positionSelect.value;

            const formData = new FormData();
            formData.append('file', uploadedFile);
            formData.append('position', position);
            formData.append('apiKey', 'asd'); // Replace with actual API key
            formData.append('fontSize', fontSize);
            formData.append('platform', 'frontend');

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/numberPages', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    alert(i18next.t('number.error_numbering', { error: errorText }));
                    return;
                }

                const blob = await response.blob();
                if (numberedPdfBlobUrl) URL.revokeObjectURL(numberedPdfBlobUrl);
                numberedPdfBlobUrl = URL.createObjectURL(blob);
                uploadedFile = new File([blob], 'numbered.pdf', {
                    type: 'application/pdf'
                });
                await renderPdf(await blob.arrayBuffer());
                downloadBtn.style.display = 'inline-block';
            } catch (err) {
                alert(i18next.t('number.error_failed', { error: err.message }));
            }
        });

        async function renderPdf(arrayBuffer) {
            currentPdf = await pdfjsLib.getDocument({
                data: arrayBuffer
            }).promise;
            pageList.innerHTML = '';

            for (let i = 1; i <= currentPdf.numPages; i++) {
                const page = await currentPdf.getPage(i);
                const viewport = page.getViewport({
                    scale: 0.3
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
                pageItem.appendChild(canvas);
                pageItem.setAttribute('data-page-number', i);

                // Show modal on hover
                pageItem.addEventListener('mouseenter', () => showModal(i));
                pageItem.addEventListener('mouseleave', () => hideModal());

                pageList.appendChild(pageItem);
            }
        }

        async function showModal(pageNumber) {
            if (!currentPdf) return;

            const page = await currentPdf.getPage(pageNumber);
            const scale = 2.5; // Bigger scale for modal
            const viewport = page.getViewport({
                scale
            });

            modalCanvas.width = viewport.width;
            modalCanvas.height = viewport.height;

            const ctx = modalCanvas.getContext('2d');
            ctx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);

            await page.render({
                canvasContext: ctx,
                viewport
            }).promise;

            modalOverlay.style.display = 'flex';
        }

        function hideModal() {
            modalOverlay.style.display = 'none';
        }

        // Also close modal on close button click
        modalCloseBtn.addEventListener('click', hideModal);

        // Close modal if clicking outside modalContent
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                hideModal();
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!numberedPdfBlobUrl) {
                alert(i18next.t('number.error_no_numbered_pdf'));
                return;
            }

            const a = document.createElement('a');
            a.href = numberedPdfBlobUrl;
            a.download = 'numbered.pdf';
            a.click();
        });
    </script>
</body>

</html>