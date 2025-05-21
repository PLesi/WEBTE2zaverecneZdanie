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
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <h2 data-i18n="rotate.title">Rotovať stránky</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />

    <div class="controls">
        <label for="rotationSelect" data-i18n="rotate.label_rotation">Uhol rotácie:</label>
        <select id="rotationSelect">
            <option value="90" data-i18n="rotate.option_90">90°</option>
            <option value="180" data-i18n="rotate.option_180">180°</option>
            <option value="270" data-i18n="rotate.option_270">270°</option>
        </select>
        <button id="rotateBtn" data-i18n="rotate.rotate_button">Rotovať</button>
        <button id="downloadBtn" style="display:none" data-i18n="rotate.download_button">Stiahnuť rotované PDF</button>
    </div>

    <div id="pageList"></div>

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

        const pdfFileInput = document.getElementById('pdfFile');
        const rotationSelect = document.getElementById('rotationSelect');
        const rotateBtn = document.getElementById('rotateBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const pageList = document.getElementById('pageList');

        pdfFileInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file || file.type !== 'application/pdf') {
                alert('Please select a valid PDF.');
                return;
            }
            uploadedFile = file;
            const buffer = await file.arrayBuffer();
            renderPdf(buffer);
        });

        rotateBtn.addEventListener('click', async () => {
            if (!uploadedFile) {
                alert('Please upload a PDF first.');
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
                    alert('Error rotating PDF: ' + errorText);
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
                alert('Rotation failed: ' + err.message);
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
                alert('No rotated PDF available to download.');
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