<?php
session_start();
if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != true) {
    header("Location: login_form.php");
}
$apiKey = $_SESSION['api_key'] ?? null;
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="operations.rearrange">Preskupiť stránky</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">

</head>

<body>
    <!-- Navigation bar -->
    <?php include 'navbarPDFoperations.php'; ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-4" data-i18n="rearrange.instruction">Presuňte stránky pre zmenu poradia</h1>
            <div id="errorMessage">
                <span id="errorText"></span>
                <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
            </div>
            <input type="file" id="pdfInput" accept="application/pdf" required />
            <label for="pdfInput" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="compress.upload_label">Nahraj PDF</span>
            </label>
            <div id="fileInfo" class="ms-3">
                <span id="fileNameDisplay" class="text-white"></span>
                <span id="originalSizeDisplay" class="text-white"></span>
            </div>
            <div id="pageList"></div>
            <div class="d-flex justify-content-center">
                <button id="downloadBtn" class="btn btn-primary mt-3" disabled data-i18n="rearrange.download_button">Stiahnuť PDF</button>
            </div>
        </div>
    </div>

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
        const pdfFileInput = document.getElementById('pdfInput');
        const downloadBtn = document.getElementById('downloadBtn');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const originalSizeDisplay = document.getElementById('originalSizeDisplay');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        function showErrorMessage(message) {
            if (errorText) errorText.textContent = message;
            if (errorMessage) errorMessage.classList.add('show');
            setTimeout(hideErrorMessage, 5000);
        }

        function hideErrorMessage() {
            if (errorMessage) errorMessage.classList.remove('show');
            if (errorText) errorText.textContent = '';
        }

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
               formData.append('apiKey', <?= json_encode($apiKey) ?> );// potom api kluce
                formData.append('platform', 'frontend');

                const response = await fetch('https://node75.webte.fei.stuba.sk/api/pdf/rearrange', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    showErrorMessage(i18next.t('rearrange.error_rearrange', { error: text }) || 'Chyba pri preskupení PDF: ' + text);
                    return;
                }

                const pdfBlob = await response.blob();
                if (currentPdfBlobUrl) URL.revokeObjectURL(currentPdfBlobUrl);
                currentPdfBlobUrl = URL.createObjectURL(pdfBlob);

            } catch (err) {
                showErrorMessage(i18next.t('rearrange.error_failed', { error: err.message }) || 'Preskupenie zlyhalo: ' + err.message);
            }
        }

        downloadBtn.addEventListener('click', () => {
            if (!currentPdfBlobUrl) {
                showErrorMessage(i18next.t('rearrange.error_no_pdf') || 'Nie je k dispozícii PDF na stiahnutie');
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