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
    <title data-i18n="operations.split">Rozdeliť PDF</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">

    <style>
        .btn-primary {
            background-color: #837ee3;
            border-color: #948de7;
            color: #000000;
        }
        .btn-primary:hover {
            background-color: #b4acee;
            border-color: #b4acee;
            color: #313038;
        }
        #pdfFile {
            display: none;
        }
        #fileInfo {
            margin-top: 10px;
            color: #c3bcf2;
        }
        #fileNameDisplay {
            font-weight: bold;
        }
        #originalSizeDisplay {
            margin-left: 10px;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            background-color: #313038;
            border-color: #c3bcf2;
            color: #c3bcf2;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .custom-file-upload:hover {
            background-color: #47464d;
        }
        .custom-file-upload i {
            margin-right: 8px;
        }
        #errorMessage {
            display: none;
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            position: relative;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        #errorMessage.show {
            display: block;
        }
        #errorMessage .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #721c24;
        }
    </style>
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
            <input type="file" id="pdfFile" accept="application/pdf" required>
            <label for="pdfFile" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="split.placeholder_upload">Nahraj PDF</span>
            </label>
            <div id="fileInfo" class="ms-3">
                <span id="originalSizeDisplay" class="text-white"></span>
            </div>
            <div class="d-flex justify-content-center">
                <button id="uploadBtn" type="submit" class="btn btn-primary mt-3" data-i18n="split.upload_button">Nahrať a rozdeliť</button>
            </div>
        </form>
        <div id="fileInfoOutput"></div>
        <button id="downloadBtn" class="btn btn-primary" style="margin-top: 1rem;" data-i18n="split.download_button">Stiahnuť ZIP</button>
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
    const pdfFileInput = document.getElementById('pdfFile');
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
            originalSizeDisplay.textContent = '';
            showErrorMessage(i18next.t('split.error_invalid_file'));
        }
    };

    pdfFileInput.addEventListener('change', () => {
        const file = pdfFileInput.files[0];
        handleFile(file);
    });

    uploadBtn.addEventListener('click', async () => {
        const file = pdfFileInput.files[0];
        if (!file || file.type !== 'application/pdf') {
            showErrorMessage(i18next.t('split.error_invalid_file'));
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('apiKey', 'asd'); // Replace with actual key
        formData.append('platform', 'frontend');

        try {
            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/split', {
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
            fileInfoDiv.innerHTML = i18next.t('split.file_info', { file: 'split_pdfs.zip', size: sizeKB });

            downloadBtn.style.display = 'inline-block';
        } catch (err) {
            showErrorMessage(i18next.t('split.error_upload', { error: err.message }));
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