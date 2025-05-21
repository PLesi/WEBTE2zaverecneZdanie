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
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
<!-- Navigation bar -->
<?php include 'navbarPDFoperations.php'; ?>

<h2 data-i18n="split.instruction">Nahrať PDF na rozdelenie</h2>
<input type="file" id="pdfFile" accept="application/pdf" data-i18n-placeholder="split.placeholder_upload" />
<br><br>
<button id="uploadBtn" data-i18n="split.upload_button">Nahrať a rozdeliť</button>

<div id="fileInfo"></div>
<button id="downloadBtn" data-i18n="split.download_button">Stiahnuť ZIP</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS with defer -->
<script defer src="../assets/js/i18n.js"></script>
<script>
    const uploadBtn = document.getElementById('uploadBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const pdfFileInput = document.getElementById('pdfFile');
    const fileInfoDiv = document.getElementById('fileInfo');

    let zipBlobUrl = null;

    uploadBtn.addEventListener('click', async () => {
        const file = pdfFileInput.files[0];
        if (!file || file.type !== 'application/pdf') {
            alert(i18next.t('split.error_invalid_file'));
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
                alert(i18next.t('split.error_response', { error: errorText }));
                return;
            }

            const zipBlob = await response.blob();
            if (zipBlobUrl) URL.revokeObjectURL(zipBlobUrl);
            zipBlobUrl = URL.createObjectURL(zipBlob);

            const sizeKB = (zipBlob.size / 1024).toFixed(2);
            fileInfoDiv.innerHTML = i18next.t('split.file_info', { file: 'split_pdfs.zip', size: sizeKB });

            downloadBtn.style.display = 'inline-block';
        } catch (err) {
            alert(i18next.t('split.error_upload', { error: err.message }));
        }
    });

    downloadBtn.addEventListener('click', () => {
        if (!zipBlobUrl) {
            alert(i18next.t('split.error_no_zip'));
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