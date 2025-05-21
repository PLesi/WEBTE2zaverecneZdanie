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
    <title data-i18n="operations.jpg_to_pdf">JPG do PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
    <style>
        #imagePreview {
            display: none;
            max-width: 100%;
            height: auto;
            margin: 20px auto;
        }

        #pdfPreview {
            display: none;
            width: 100%;
            height: 400px;
            margin: 20px auto;
        }
    </style>
</head>

<body>
<!-- Navigačný panel -->
<?php include 'navbarPDFoperations.php'; ?>
<div class="hero-section">
    <div class="container mt-5">
        <h1 class="display-4" data-i18n="operations.jpg_to_pdf">PDF do JPG</h1>
        <p class="lead" data-i18n="jpg_to_pdf.title">Konvertuj JPG to PDF s náhľadom</p>

        <!-- Chybové hlásenie -->
        <div id="errorMessage">
            <span id="errorText"></span>
            <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
        </div>

        <div class="text-center mb-3">
            <!-- Skrytý input -->
            <input type="file" id="jpgFile" accept="image/jpeg" />

            <!-- Vlastné tlačidlo s ikonou -->
            <label for="jpgFile" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="jpg_to_pdf.upload_label">Nahraj JPG</span>
            </label>

            <!-- Zobrazenie názvu súboru -->
            <div id="fileInfo" class="mt-2">
                <span id="fileNameDisplay"></span>
            </div>
        </div>

        <img id="imagePreview" alt="Image preview" />

        <div class="text-center mb-3">
            <button id="convertBtn" class="btn btn-primary" disabled data-i18n="jpg_to_pdf.convert_button">Convert to PDF</button>
        </div>

        <embed id="pdfPreview" type="application/pdf" />
        <div class="text-center">
            <button id="downloadBtn" class="btn btn-success" style="display:none" data-i18n="jpg_to_pdf.download_button">Download PDF</button>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS -->
<script src="../assets/js/i18n.js"></script>

<script>
    const jpgFileInput = document.getElementById('jpgFile');
    const imagePreview = document.getElementById('imagePreview');
    const convertBtn = document.getElementById('convertBtn');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    let pdfPreview = document.getElementById('pdfPreview');
    const downloadBtn = document.getElementById('downloadBtn');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    let pdfBlobUrl = null;

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

    jpgFileInput.addEventListener('change', () => {
        pdfPreview.style.display = 'none';
        downloadBtn.style.display = 'none';
        convertBtn.disabled = true;
        pdfBlobUrl && URL.revokeObjectURL(pdfBlobUrl);
        pdfBlobUrl = null;

        const file = jpgFileInput.files[0];
        if (!file) {
            fileNameDisplay.textContent = '';
            imagePreview.style.display = 'none';
            return;
        }
        if (file.type !== 'image/jpeg') {
            fileNameDisplay.textContent = '';
            imagePreview.style.display = 'none';
            showErrorMessage(i18next.t('jpg_to_pdf.error_invalid_file'));
            return;
        }

        fileNameDisplay.textContent = file.name;

        // Show image preview
        const reader = new FileReader();
        reader.onload = e => {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
            convertBtn.disabled = false;
        };
        reader.onerror = () => {
            showErrorMessage(i18next.t('jpg_to_pdf.error_read_file'));
        };
        reader.readAsDataURL(file);
    });

    convertBtn.addEventListener('click', async () => {
        const file = jpgFileInput.files[0];
        if (!file) {
            showErrorMessage(i18next.t('jpg_to_pdf.error_no_file'));
            return;
        }

        convertBtn.disabled = true;
        convertBtn.textContent = i18next.t('jpg_to_pdf.converting');
        downloadBtn.style.display = 'none';
        pdfPreview.style.display = 'none';
        pdfBlobUrl && URL.revokeObjectURL(pdfBlobUrl);
        pdfBlobUrl = null;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('apiKey', 'asd'); // Replace with real key if needed
        formData.append('platform', 'frontend');

        try {
            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/jpgToPdf', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                showErrorMessage(i18next.t('jpg_to_pdf.error_convert', { error: errorText }));
                return;
            }

            const blob = await response.blob();
            pdfBlobUrl = URL.createObjectURL(blob);

            // Show PDF preview
            pdfPreview.src = pdfBlobUrl;
            pdfPreview.style.display = 'block';
            pdfPreview.alt = 'Converted PDF Preview (embedded)';

            const embed = document.createElement('embed');
            embed.src = pdfBlobUrl;
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '400px';
            pdfPreview.replaceWith(embed);
            pdfPreview = embed;

            downloadBtn.style.display = 'block';
            showErrorMessage(i18next.t('jpg_to_pdf.success_message'));

        } catch (err) {
            showErrorMessage(i18next.t('jpg_to_pdf.error_failed', { error: err.message }));
        } finally {
            convertBtn.disabled = false;
            convertBtn.textContent = i18next.t('jpg_to_pdf.convert_button');
        }
    });

    downloadBtn.addEventListener('click', () => {
        if (!pdfBlobUrl) {
            showErrorMessage(i18next.t('jpg_to_pdf.error_no_pdf'));
            return;
        }
        const a = document.createElement('a');
        a.href = pdfBlobUrl;
        a.download = 'converted.pdf';
        document.body.appendChild(a);
        a.click();
        a.remove();
    });
</script>
</body>

</html>