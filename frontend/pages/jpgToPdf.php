<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Convert JPG to PDF with Preview</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <h2>Convert JPG to PDF with Preview</h2>

    <label for="jpgFile">Select JPG file:</label>
    <input type="file" id="jpgFile" accept="image/jpeg" />

    <img id="imagePreview" alt="Image preview" style="display:none" />

    <button id="convertBtn" disabled>Convert to PDF</button>
    <div id="message"></div>

    <img id="pdfPreview" alt="PDF preview" style="display:none" />
    <button id="downloadBtn">Download PDF</button>

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
        const message = document.getElementById('message');
        let pdfPreview = document.getElementById('pdfPreview');
        const downloadBtn = document.getElementById('downloadBtn');

        let pdfBlobUrl = null;

        jpgFileInput.addEventListener('change', () => {
            message.textContent = '';
            pdfPreview.style.display = 'none';
            downloadBtn.style.display = 'none';
            convertBtn.disabled = true;
            pdfBlobUrl && URL.revokeObjectURL(pdfBlobUrl);
            pdfBlobUrl = null;

            let file = jpgFileInput.files[0];
            if (!file) {
                imagePreview.style.display = 'none';
                return;
            }
            if (file.type !== 'image/jpeg') {
                message.textContent = 'File must be a JPG image.';
                imagePreview.style.display = 'none';
                return;
            }

            // Show image preview
            const reader = new FileReader();
            reader.onload = e => {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
            convertBtn.disabled = false;
        });

        convertBtn.addEventListener('click', async () => {
            message.textContent = '';
            const file = jpgFileInput.files[0];
            if (!file) {
                message.textContent = 'Please select a JPG file.';
                return;
            }

            convertBtn.disabled = true;
            convertBtn.textContent = 'Converting...';
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
                    message.textContent = 'Error: ' + errorText;
                    convertBtn.disabled = false;
                    convertBtn.textContent = 'Convert to PDF';
                    return;
                }

                const blob = await response.blob();
                pdfBlobUrl = URL.createObjectURL(blob);

                // Show PDF preview: simplest way is embed as an object or img of first page
                // Since rendering PDF's first page as image is complex in pure JS without libs,
                // we will embed PDF in <embed> or <object> tag as preview instead.

                pdfPreview.src = pdfBlobUrl;
                pdfPreview.style.display = 'block';
                pdfPreview.alt = 'Converted PDF Preview (embedded)';
                // For better preview, we can embed PDF using <embed> or <object> dynamically
                // but <img> won't show PDF. Let's switch to <embed> for preview:
                const embed = document.createElement('embed');
                embed.src = pdfBlobUrl;
                embed.type = 'application/pdf';
                embed.style.width = '100%';
                embed.style.height = '400px';
                pdfPreview.replaceWith(embed);
                pdfPreview = embed;

                downloadBtn.style.display = 'block';
                message.style.color = 'green';
                message.textContent = 'Conversion successful!';

            } catch (err) {
                message.textContent = 'Error: ' + err.message;
            } finally {
                convertBtn.disabled = false;
                convertBtn.textContent = 'Convert to PDF';
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!pdfBlobUrl) {
                alert('No PDF to download.');
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