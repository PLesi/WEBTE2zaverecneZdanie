<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PDF-compression</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar -->
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>PDF-compression</h1>
        <form id="pdfForm" method="post" enctype="multipart/form-data">
            <label for="pdfInput">Upload PDF:</label><br />
            <input type="file" id="pdfInput" accept="application/pdf" required /><br />
            <p id="povodna-velkost"></p>

            <label for="compressionLevel">Compression Level (1-9):</label><br />
            <input type="number" id="compressionLevel" min="1" max="9" value="5" required /><br />

            <button type="submit">Compress</button>
        </form>

        <div class="pdf-preview" id="previewContainer">
            <h3>Compressed PDF Preview:</h3>
            <p id="compressed-size"></p>
            <div id="pdfPages" class="pdf-grid"></div>
            <button id="downloadBtn" style="margin-top: 1rem;">Download</button>
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
        const originalSizeText = document.getElementById('povodna-velkost');
        const compressedSizeText = document.getElementById('compressed-size');

        let compressedBlob = null;

        pdfInput.addEventListener('change', () => {
            const file = pdfInput.files[0];
            if (file) {
                const sizeKB = (file.size / 1024).toFixed(2);
                originalSizeText.innerText = `Original Size: ${sizeKB} KB`;
            } else {
                originalSizeText.innerText = '';
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const file = pdfInput.files[0];
            if (!file) return alert('Please upload a PDF.');

            const level = compressionLevel.value;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('level', level);

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/compressNew', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Compression failed');

                compressedBlob = await response.blob();
                const blobUrl = URL.createObjectURL(compressedBlob);

                const sizeKB = (compressedBlob.size / 1024).toFixed(2);
                compressedSizeText.innerText = `Compressed Size: ${sizeKB} KB`;
                previewContainer.style.display = 'flex';

                // Clear previous pages
                pdfPagesContainer.innerHTML = '';

                // Load PDF with PDF.js and render pages
                const pdf = await pdfjsLib.getDocument(blobUrl).promise;
                for (let i = 1; i <= pdf.numPages; i++) {
                    const page = await pdf.getPage(i);
                    const viewport = page.getViewport({
                        scale: 0.6
                    }); // smaller scale for thumbnails

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
                alert(err.message);
            }
        });

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