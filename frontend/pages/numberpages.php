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
    <title data-i18n="number.title">Číslovať stránky</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
    <style>
        .controls {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-bottom: 20px;
            padding: 10px;
        }

        .controls label {
            margin-right: 10px;
            font-weight: bold;
        }

        .controls select,
        .controls input[type="number"],
        .controls button {
            max-width: 200px;
        }

        #pageList {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        #modalOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #modalContent {
            position: relative;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 90%;
            max-height: 90vh;
            overflow: auto;
        }

        #modalCloseBtn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #000;
        }

        #modalCanvas {
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
<!-- Navigačný panel -->
<?php include 'navbarPDFoperations.php'; ?>
<div class="hero-section">
    <div class="container mt-5">
        <h1 class="display-4" data-i18n="number.title">Číslovať stránky</h1>

        <!-- Chybové hlásenie -->
        <div id="errorMessage">
            <span id="errorText"></span>
            <button type="button" class="close-btn" onclick="hideErrorMessage()">×</button>
        </div>

        <div class="text-center mb-3">
            <!-- Skrytý input pre súbor -->
            <input type="file" id="pdfInput" accept="application/pdf" />

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

        <div class="controls">
            <div class="d-flex align-items-center gap-2">
                <label for="positionSelect" data-i18n="number.label_position">Pozícia číslovania:</label>
                <select id="positionSelect" class="form-select">
                    <option value="bottomRight" data-i18n="number.option_bottomRight">Spodná pravá</option>
                    <option value="bottomLeft" data-i18n="number.option_bottomLeft">Spodná ľavá</option>
                    <option value="topLeft" data-i18n="number.option_topLeft">Horná ľavá</option>
                    <option value="topRight" data-i18n="number.option_topRight">Horná pravá</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label for="fontSize" data-i18n="number.label_font_size">Veľkosť písma:</label>
                <input type="number" id="fontSize" class="form-control" placeholder="Veľkosť písma" min="8" max="42" value="13" />
            </div>
            <button id="numberBtn" class="btn btn-primary" data-i18n="number.number_button">Číslovať stránky</button>
            <button id="downloadBtn" class="btn btn-success" style="display:none" data-i18n="number.download_button">Stiahnuť číslované PDF</button>
        </div>

        <div id="pageList" class="row justify-content-center"></div>

        <!-- Modal for big page preview -->
        <div id="modalOverlay">
            <div id="modalContent">
                <button id="modalCloseBtn" title="Close">×</button>
                <canvas id="modalCanvas"></canvas>
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
    const pdfFileInput = document.getElementById('pdfInput');
    const positionSelect = document.getElementById('positionSelect');
    const numberBtn = document.getElementById('numberBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const pageList = document.getElementById('pageList');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    const modalOverlay = document.getElementById('modalOverlay');
    const modalCanvas = document.getElementById('modalCanvas');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

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

    pdfFileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) {
            fileNameDisplay.textContent = '';
            return;
        }
        if (file.type !== 'application/pdf') {
            fileNameDisplay.textContent = '';
            showErrorMessage(i18next.t('number.error_invalid_file'));
            pdfFileInput.value = ''; // Reset inputu
            return;
        }
        fileNameDisplay.textContent = file.name;
        uploadedFile = file;
        const buffer = await file.arrayBuffer();
        await renderPdf(buffer);
    });

    numberBtn.addEventListener('click', async () => {
        if (!uploadedFile) {
            showErrorMessage(i18next.t('number.error_no_file'));
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
            numberBtn.disabled = true;
            numberBtn.textContent = i18next.t('number.numbering');

            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/numberPages', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                showErrorMessage(i18next.t('number.error_numbering', { error: errorText }));
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
            showErrorMessage(i18next.t('number.error_failed', { error: err.message }));
        } finally {
            numberBtn.disabled = false;
            numberBtn.textContent = i18next.t('number.number_button');
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
            showErrorMessage(i18next.t('number.error_no_numbered_pdf'));
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