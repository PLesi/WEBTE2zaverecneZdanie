<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Split PDF into ZIP</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation bar -->
    <?php include 'navbar.php'; ?>

    <h2>Upload PDF to Split</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />
    <br><br>
    <button id="uploadBtn">Upload & Split</button>

    <div id="fileInfo"></div>
    <button id="downloadBtn">Download ZIP</button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>
    <script>
        const uploadBtn = document.getElementById('uploadBtn');
        const downloadBtn = document.getElementById('downloadBtn');
        const pdfFileInput = document.getElementById('pdfFile');
        const fileInfoDiv = document.getElementById('fileInfo');

        let zipBlobUrl = null;

        uploadBtn.addEventListener('click', async () => {
            const file = pdfFileInput.files[0];
            if (!file || file.type !== 'application/pdf') {
                alert('Please select a valid PDF file.');
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
                    alert('Error: ' + errorText);
                    return;
                }

                const zipBlob = await response.blob();
                if (zipBlobUrl) URL.revokeObjectURL(zipBlobUrl);
                zipBlobUrl = URL.createObjectURL(zipBlob);

                const sizeKB = (zipBlob.size / 1024).toFixed(2);
                fileInfoDiv.innerHTML = `Received file: <strong>split_pdfs.zip</strong> (${sizeKB} KB)`;

                downloadBtn.style.display = 'inline-block';
            } catch (err) {
                alert('Upload failed: ' + err.message);
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!zipBlobUrl) {
                alert('No ZIP file ready for download.');
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