<?php
/*session_start();
if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != true) {
    header("Location: login_form.php");
}
*/?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="delete_page.title">Odstrániť stránku</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
</head>

<body>
<!-- Navigation bar -->
<?php include 'navbarPDFoperations.php'; ?>
<div class="hero-section">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-4" data-i18n="delete_page.title">Odstrániť stránku</h1>
                <p class="lead" data-i18n="delete_page.instruction">Kliknite na stránku pre jej odstránenie</p>

                <!-- Chybové hlásenie -->
                <div id="errorMessage">
                    <span id="errorText"></span>
                    <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
                </div>

                <div class="text-center mb-3">
                    <!-- Skrytý input -->
                    <input type="file" id="pdfInput" accept="application/pdf" required />

                    <!-- Vlastné tlačidlo s ikonou -->
                    <label for="pdfInput" class="custom-file-upload">
                        <i class="bi bi-upload"></i>
                        <span data-i18n="compress.upload_label">Nahraj PDF</span>
                    </label>

                    <!-- Zobrazenie názvu súboru -->
                    <div id="fileInfo" class="mt-2">
                        <span id="fileNameDisplay"></span>
                    </div>
                </div>

                <div id="pageList" class="d-flex flex-wrap justify-content-center"></div>
                <div class="text-center mt-3">
                    <button id="downloadBtn" class="btn btn-primary" disabled data-i18n="delete_page.download_button">Stiahnuť PDF</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.8.162/pdf.min.js"></script>
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
    let currentPdfBlobUrl = null;
    let fileObject = null;

    const pdfFileInput = document.getElementById('pdfInput');
    const pageList = document.getElementById('pageList');
    const downloadBtn = document.getElementById('downloadBtn');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    // Funkcia na zobrazenie chybového hlásenia
    function showErrorMessage(message) {
        errorText.textContent = message;
        errorMessage.classList.add('show');
        // Automaticky skryť po 5 sekundách
        setTimeout(hideErrorMessage, 5000);
    }

    // Funkcia na skrytie chybového hlásenia
    function hideErrorMessage() {
        errorMessage.classList.remove('show');
        errorText.textContent = '';
    }

    // Funkcia na spracovanie nahraného súboru
    const handleFile = (file) => {
        if (file && file.type === 'application/pdf') {
            fileNameDisplay.textContent = file.name;
            fileObject = file;
            originalPdfBytes = null; // Reset pre nový súbor
        } else {
            fileNameDisplay.textContent = '';
            fileObject = null;
            showErrorMessage(i18next.t('delete_page.error_invalid_file'));
        }
    };

    // Manuálny výber súboru
    pdfFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        handleFile(file);
        if (fileObject) {
            renderPdf(fileObject);
        }
    });

    async function renderPdf(file) {
        originalPdfBytes = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({
            data: originalPdfBytes
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
            overlay.innerText = i18next.t('delete_page.delete_label');

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
                showErrorMessage(i18next.t('delete_page.error_remove', { error: errText }));
                return;
            }

            const newPdfBlob = await response.blob();
            if (currentPdfBlobUrl) URL.revokeObjectURL(currentPdfBlobUrl);
            currentPdfBlobUrl = URL.createObjectURL(newPdfBlob);

            fileObject = new File([newPdfBlob], "updated.pdf", {
                type: "application/pdf"
            });
            const newBytes = await newPdfBlob.arrayBuffer();
            await renderPdf(fileObject);

        } catch (err) {
            showErrorMessage(i18next.t('delete_page.error_failed', { error: err.message }));
        }
    }

    downloadBtn.addEventListener('click', () => {
        if (!currentPdfBlobUrl) {
            showErrorMessage(i18next.t('delete_page.error_no_updated_pdf'));
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