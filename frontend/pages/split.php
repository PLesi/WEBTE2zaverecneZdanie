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
    <title data-i18n="operations.split">Rozdeliť PDF</title>

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
    <div class="container">
        <h1 class="display-4" data-i18n="split.instruction">Nahrať PDF na rozdelenie</h1>
        <div id="errorMessage">
            <span id="errorText"></span>
            <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
        </div>
        <form id="pdfForm" method="post" enctype="multipart/form-data">
            <div class="text-center mb-3">
                <!-- Skrytý input pre súbor -->
                <input type="file" id="pdfInput" accept="application/pdf" required>

                <!-- Vlastné tlačidlo s ikonou -->
                <label for="pdfInput" class="custom-file-upload">
                    <i class="bi bi-upload"></i>
                    <span data-i18n="split.placeholder_upload">Nahraj PDF</span>
                </label>

                <!-- Zobrazenie názvu súboru -->
                <div id="fileInfo" class="mt-2">
                    <span id="fileNameDisplay"></span>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 mt-3 flex-wrap">
                <button id="uploadBtn" type="submit" class="btn btn-primary" data-i18n="split.upload_button">
                    Nahrať a rozdeliť
                </button>
                <button id="downloadBtn" class="btn btn-primary" data-i18n="split.download_button">
                    Stiahnuť ZIP
                </button>
            </div>
        </form>

        <div id="fileInfoOutput"></div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS with defer -->
<script defer src="../assets/js/i18n.js"></script>
<script>
    const form = document.getElementById('pdfForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const pdfFileInput = document.getElementById('pdfInput');
    const fileInfoDiv = document.getElementById('fileInfo');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    const fileInfoOutput = document.getElementById('fileInfoOutput');

    let zipBlobUrl = null;

    function showErrorMessage(message) {
        errorText.textContent = message;
        errorMessage.classList.add('show');
        setTimeout(hideErrorMessage, 5000);
    }

    function hideErrorMessage() {
        errorMessage.classList.remove('show');
        errorText.textContent = '';
    }

    const handleFile = (file) => {
        if (file && file.type === 'application/pdf') {
            fileNameDisplay.textContent = file.name;
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            pdfFileInput.files = dataTransfer.files;
        } else {
            fileNameDisplay.textContent = '';
            showErrorMessage(i18next.t('split.error_invalid_file'));
            pdfFileInput.value = ''; // Reset inputu
        }
    };

    pdfFileInput.addEventListener('change', () => {
        const file = pdfFileInput.files[0];
        handleFile(file);
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault(); // Prevent default form submission
        const file = pdfFileInput.files[0];
        if (!file || file.type !== 'application/pdf') {
            showErrorMessage(i18next.t('split.error_invalid_file'));
            return;
        }

        const formData = new FormData(form);
        formData.append('apiKey', <?= json_encode($apiKey) ?> ); // Replace with actual key
        formData.append('platform', 'frontend');
          formData.append('file', file);
        try {
            uploadBtn.disabled = true;
            uploadBtn.textContent = i18next.t('split.uploading');

            const response = await fetch('https://node75.webte.fei.stuba.sk/api/pdf/split', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                showErrorMessage(i18next.t('split.error_response', { error: errorText }));
                return;
            }

            const zipBlob = await response.blob();
            if (zipBlobUrl) URL.revokeObjectURL(zipBlobUrl);
            zipBlobUrl = URL.createObjectURL(zipBlob);

            const sizeKB = (zipBlob.size / 1024).toFixed(2);
            fileInfoOutput.innerHTML = i18next.t('split.file_info', { file: 'split_pdfs.zip', size: sizeKB });

            downloadBtn.style.display = 'inline-block';
        } catch (err) {
            showErrorMessage(i18next.t('split.error_upload', { error: err.message }));
        } finally {
            uploadBtn.disabled = false;
            uploadBtn.textContent = i18next.t('split.upload_button');
        }
    });

    downloadBtn.addEventListener('click', () => {
        if (!zipBlobUrl) {
            showErrorMessage(i18next.t('split.error_no_zip'));
            return;
        }

        const a = document.createElement('a');
        a.href = zipBlobUrl;
        a.download = 'split_pdfs.zip';
        a.click();
    });
</script>
</body>

</html>