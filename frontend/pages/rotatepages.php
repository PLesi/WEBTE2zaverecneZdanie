<?php
session_start();
if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != true) {
    header("Location: login_form.php");
}
?>

<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="operations.rotate">Rotovať stránky</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-4" data-i18n="rotate.title">Rotovať stránky</h1>
            <div id="errorMessage">
                <span id="errorText"></span>
                <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
            </div>
            <input type="file" id="pdfInput" accept="application/pdf">
            <label for="pdfInput" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="compress.upload_label">Nahraj PDF</span>
            </label>
            <div id="fileInfo" class="ms-3">
                <span id="fileNameDisplay" class="text-white"></span>
                <span id="originalSizeDisplay" class="text-white"></span>
            </div>
            <div class="controls">
                <label for="rotationSelect" data-i18n="rotate.label_rotation">Uhol rotácie:</label>
                <select id="rotationSelect" class="form-select" style="width: auto; display: inline-block;">
                    <option value="90" data-i18n="rotate.option_90">90°</option>
                    <option value="180" data-i18n="rotate.option_180">180°</option>
                    <option value="270" data-i18n="rotate.option_270">270°</option>
                </select>
                <button id="rotateBtn" class="btn btn-primary" data-i18n="rotate.rotate_button">Rotovať</button>
                <button id="downloadBtn" class="btn btn-primary" style="display: none;" data-i18n="rotate.download_button">Stiahnuť rotované PDF</button>
            </div>
            <div id="pageList"></div>
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
        let rotatedPdfBlobUrl = null;

        const pdfFileInput = document.getElementById('pdfInput');
        const rotationSelect = document.getElementById('rotationSelect');
        const rotateBtn = document.getElementById('rotateBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const pageList = document.getElementById('pageList');
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

        const handleFile = (file) => {
            try {
                if (file && file.type === 'application/pdf') {
                    if (fileNameDisplay) fileNameDisplay.textContent = file.name;
                    uploadedFile = file;
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    pdfFileInput.files = dataTransfer.files;
                    file.arrayBuffer().then(renderPdf).catch(err => {
                        showErrorMessage('Chyba pri načítaní PDF: ' + err.message);
                    });
                } else {
                    if (fileNameDisplay) fileNameDisplay.textContent = '';
                    if (originalSizeDisplay) originalSizeDisplay.textContent = '';
                    showErrorMessage(i18next.t('rotate.error_invalid_file') || 'Neplatný súbor, vyberte PDF');
                }
            } catch (err) {
                showErrorMessage('Chyba pri spracovaní súboru: ' + err.message);
            }
        };

        pdfFileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            handleFile(file);
        });

        rotateBtn.addEventListener('click', async () => {
            if (!uploadedFile) {
                showErrorMessage(i18next.t('rotate.error_no_file') || 'Najprv nahrajte PDF');
                return;
            }

            const angle = parseInt(rotationSelect.value);

            const formData = new FormData();
            formData.append('file', uploadedFile);
            formData.append('degree', angle);
            formData.append('apiKey', 'asd'); // Replace with actual API key
            formData.append('platform', 'frontend');

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/rotatePages', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    showErrorMessage(i18next.t('rotate.error_failed') || 'Chyba pri rotácii PDF: ' + errorText);
                    return;
                }

                const blob = await response.blob();
                if (rotatedPdfBlobUrl) URL.revokeObjectURL(rotatedPdfBlobUrl);
                rotatedPdfBlobUrl = URL.createObjectURL(blob);
                uploadedFile = new File([blob], 'rotated.pdf', {
                    type: 'application/pdf'
                });
                renderPdf(await blob.arrayBuffer());
                downloadBtn.style.display = 'inline-block';
            } catch (err) {
                showErrorMessage(i18next.t('rotate.error_failed') || 'Rotácia zlyhala: ' + err.message);
            }
        });

        async function renderPdf(arrayBuffer) {
            const pdf = await pdfjsLib.getDocument({
                data: arrayBuffer
            }).promise;
            pageList.innerHTML = '';

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
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
                pageList.appendChild(pageItem);
            }
        }

        downloadBtn.addEventListener('click', () => {
            if (!rotatedPdfBlobUrl) {
                showErrorMessage(i18next.t('rotate.error_no_rotated') || 'Nie je k dispozícii rotované PDF na stiahnutie');
                return;
            }

            const a = document.createElement('a');
            a.href = rotatedPdfBlobUrl;
            a.download = 'rotated.pdf';
            a.click();
        });
    </script>
</body>

</html>