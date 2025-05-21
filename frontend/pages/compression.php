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
    <title data-i18n="compress.title">Komprimovať PDF</title>

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

<!-- Main content -->
<div class="hero-section">
    <div class="container">
        <h1 class="display-4" data-i18n="compress.title">Kompresia PDF</h1>
        <p class="lead" data-i18n="compress.description">Nahrajte PDF súbor a nastavte úroveň kompresie pre optimalizáciu veľkosti.</p>

        <!-- Chybové hlásenie -->
        <div id="errorMessage">
            <span id="errorText"></span>
            <button type="button" class="close-btn" onclick="hideErrorMessage()">&times;</button>
        </div>

        <form id="pdfForm" method="post" enctype="multipart/form-data">
            <!-- Skrytý input -->
            <input type="file" id="pdfInput" accept="application/pdf" required />

            <!-- Vlastné tlačidlo s ikonou -->
            <label for="pdfInput" class="custom-file-upload">
                <i class="bi bi-upload"></i>
                <span data-i18n="compress.upload_label">Nahraj PDF</span>
            </label>

            <!-- Zobrazenie názvu súboru a veľkosti -->
            <div id="fileInfo" class="ms-3">
                <span id="fileNameDisplay" class="text-white"></span>
                <span id="originalSizeDisplay" class="text-white"></span>
            </div>

            <!-- Kompresia a tlačidlo vedľa seba -->
            <div class="d-flex justify-content-center">
                <div class="d-flex align-items-center gap-3 mt-3 flex-wrap justify-content-center text-center">
                    <div>
                        <label for="compressionLevel" data-i18n="compress.level_label">Úroveň kompresie (1-9):</label>
                        <input type="number" id="compressionLevel" min="1" max="9" value="5" required class="form-control mx-auto" style="width: 100px;">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3" data-i18n="compress.submit">Komprimovať</button>
                </div>
            </div>
        </form>

        <div class="pdf-preview" id="previewContainer">
            <h3 data-i18n="compress.preview_title">Náhľad komprimovaného PDF:</h3>
            <p id="compressed-size"></p>
            <div id="pdfPages" class="pdf-grid"></div>
            <button id="downloadBtn" class="btn btn-primary" style="margin-top: 1rem;" data-i18n="compress.download">Stiahnuť</button>
        </div>
    </div>
</div>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS -->
<script src="../assets/js/i18n.js"></script>
<script>
    const form = document.getElementById('pdfForm');
    const pdfInput = document.getElementById('pdfInput');
    const compressionLevel = document.getElementById('compressionLevel');
    const previewContainer = document.getElementById('previewContainer');
    const pdfPagesContainer = document.getElementById('pdfPages');
    const downloadBtn = document.getElementById('downloadBtn');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const originalSizeDisplay = document.getElementById('originalSizeDisplay');
    const compressedSizeText = document.getElementById('compressed-size');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    let compressedBlob = null;

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
            const sizeKB = (file.size / 1024).toFixed(2);
            originalSizeDisplay.textContent = i18next.t('compress.original_size', { size: sizeKB });
            // Priradiť súbor do pdfInput
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            pdfInput.files = dataTransfer.files;
        } else {
            fileNameDisplay.textContent = '';
            originalSizeDisplay.textContent = '';
            showErrorMessage(i18next.t('compress.error_no_file'));
        }
    };

    // Manuálny výber súboru
    pdfInput.addEventListener('change', () => {
        const file = pdfInput.files[0];
        handleFile(file);
    });

    // Odoslanie formulára
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const file = pdfInput.files[0];
        if (!file) {
            showErrorMessage(i18next.t('compress.error_no_file'));
            return;
        }

        const level = compressionLevel.value;

        const formData = new FormData();
        formData.append('file', file);
        formData.append('level', level);

        try {
            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/compressNew', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(i18next.t('compress.error_failed'));

            compressedBlob = await response.blob();
            const blobUrl = URL.createObjectURL(compressedBlob);

            const sizeKB = (compressedBlob.size / 1024).toFixed(2);
            compressedSizeText.textContent = i18next.t('compress.compressed_size', { size: sizeKB });
            previewContainer.style.display = 'flex';

            pdfPagesContainer.innerHTML = '';

            const pdf = await pdfjsLib.getDocument(blobUrl).promise;
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 0.6 });

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                pdfPagesContainer.appendChild(canvas);
            }

            URL.revokeObjectURL(blobUrl);
        } catch (err) {
            showErrorMessage(i18next.t('compress.error_failed'));
        }
    });

    // Stiahnutie súboru
    downloadBtn.addEventListener('click', () => {
        if (!compressedBlob) return;
        const a = document.createElement('a');
        a.href = URL.createObjectURL(compressedBlob);
        a.download = 'compressed.pdf';
        a.click();
    });
</script>
</body>

</html>