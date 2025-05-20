<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PDF-protect</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">

</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <div class="container">
        <h1>Password Protect PDF</h1>
        <form id="pdfProtectForm" method="post" enctype="multipart/form-data">
            <label for="pdfInput">Upload PDF:</label><br />
            <input type="file" id="pdfInput" accept="application/pdf" required /><br />

            <label for="password">Enter Password:</label><br />
            <input type="password" id="password" required /><br />

            <button id="downloadBtn" type="submit">Download protected PDF</button>
        </form>


    </div>

    <footer>Webove technologie - Pdf editor application</footer>

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

        let protectedBlob = null;

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
            const password = passwordInput.value;

            if (!file || !password) return alert('Please upload a PDF and enter a password.');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('password', password);

            try {
                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/protect', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) throw new Error('Password protection failed');

                protectedBlob = await response.blob();
            } catch (err) {
                alert(err.message);
            }
        });

        downloadBtn.addEventListener('click', () => {
            if (!protectedBlob) return;
            const a = document.createElement('a');
            a.href = URL.createObjectURL(protectedBlob);
            a.download = 'protected.pdf';
            a.click();
        });
    </script>
</body>

</html>