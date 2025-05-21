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
    <title data-i18n="operations.protect">Pridať heslo</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">

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
        label {
            color: #c3bcf2;
            margin-top: 15px;
            display: block;
        }
        #password {
            margin-top: 5px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #c3bcf2;
            background-color: #313038;
            color: #c3bcf2;
            width: 100%;
            max-width: 300px;
        }
        #password:focus {
            outline: none;
            border-color: #837ee3;
        }
        #originalSizeDisplay {
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4" data-i18n="protect.title">Password Protect PDF</h1>
            <div id="errorMessage">
                <span id="errorText"></span>
                <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
            </div>
            <form id="pdfProtectForm" method="post" enctype="multipart/form-data">
                <input type="file" id="pdfInput" accept="application/pdf" required>
                <label for="pdfInput" class="custom-file-upload">
                    <i class="bi bi-upload"></i>
                    <span data-i18n="compress.upload_label">Nahrať PDF</span>
                </label>
                <div id="fileInfo" class="ms-3">
                    <span id="fileNameDisplay" class="text-white"></span>
                </div>
                <label for="password" data-i18n="protect.label_password">Zadať heslo:</label>
                <input type="password" id="password" required>
                <div class="d-flex justify-content-center mt-3">
                    <button id="downloadBtn" type="submit" class="btn btn-primary" data-i18n="protect.download_button">Stiahnuť chránené PDF</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>
    <script>
        const form = document.getElementById('pdfProtectForm');
        const pdfInput = document.getElementById('pdfInput');
        const passwordInput = document.getElementById('password');
        const downloadBtn = document.getElementById('downloadBtn');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        let protectedBlob = null;

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
                    if (originalSizeDisplay) {
                        const sizeKB = (file.size / 1024).toFixed(2);
                        originalSizeDisplay.textContent = i18next.t('protect.original_size', { size: sizeKB });
                    }
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    pdfInput.files = dataTransfer.files;
                } else {
                    if (fileNameDisplay) fileNameDisplay.textContent = '';
                    if (originalSizeDisplay) originalSizeDisplay.textContent = '';
                    showErrorMessage(i18next.t('protect.error_missing_input') || 'Neplatný súbor, vyberte PDF');
                }
            } catch (err) {
                showErrorMessage('Chyba pri spracovaní súboru: ' + err.message);
            }
        };

        if (pdfInput) {
            pdfInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                handleFile(file);
            });
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const file = pdfInput.files[0];
            const password = passwordInput.value;

            if (!file || !password) {
                showErrorMessage(i18next.t('protect.error_missing_input') || 'Vyberte súbor a zadajte heslo');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('password', password);

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/protect', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error(i18next.t('protect.error_protection_failed'));

                protectedBlob = await response.blob();
            } catch (err) {
                showErrorMessage(i18next.t('protect.error', { error: err.message }) || 'Chyba: ' + err.message);
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!protectedBlob) {
                showErrorMessage(i18next.t('protect.error_no_protected_pdf') || 'Nie je k dispozícii chránené PDF');
                return;
            }
            const a = document.createElement('a');
            a.href = URL.createObjectURL(protectedBlob);
            a.download = 'protected.pdf';
            a.click();
        });
    </script>
</body>

</html>