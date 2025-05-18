<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Split PDF into ZIP</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        #fileInfo {
            margin-top: 20px;
        }

        #downloadBtn {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            display: none;
        }
    </style>
</head>

<body>
    <h2>Upload PDF to Split</h2>
    <input type="file" id="pdfFile" accept="application/pdf" />
    <br><br>
    <button id="uploadBtn">Upload & Split</button>

    <div id="fileInfo"></div>
    <button id="downloadBtn">Download ZIP</button>

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