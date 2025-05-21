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
    <title data-i18n="operations.merge">Spojiť PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <!-- Navigačný panel -->
    <?php include 'navbarPDFoperations.php'; ?>

    <h2 data-i18n="merge.title">Spojiť dva PDF súbory</h2>

    <label for="file1" data-i18n="merge.label_file1">Vyberte prvý PDF:</label>
    <input type="file" id="file1" accept="application/pdf" />

    <label for="file2" data-i18n="merge.label_file2">Vyberte druhý PDF:</label>
    <input type="file" id="file2" accept="application/pdf" />

    <button id="mergeBtn" data-i18n="merge.merge_button">Spojiť PDF</button>

    <div id="message"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>

    <script>
        const mergeBtn = document.getElementById('mergeBtn');
        const message = document.getElementById('message');

        mergeBtn.addEventListener('click', async () => {
            message.textContent = '';
            const file1 = document.getElementById('file1').files[0];
            const file2 = document.getElementById('file2').files[0];


            if (!file1 || !file2) {
                message.textContent = 'Please select both PDF files.';
                return;
            }


            if (file1.type !== 'application/pdf' || file2.type !== 'application/pdf') {
                message.textContent = 'Both files must be PDFs.';
                return;
            }

            const formData = new FormData();
            formData.append('file1', file1);
            formData.append('file2', file2);
            formData.append('apiKey', 'asd');
            formData.append('platform', 'frontend');


            try {
                mergeBtn.disabled = true;
                mergeBtn.textContent = 'Merging...';

                const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/merge', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    message.textContent = 'Error: ' + errorText;
                    mergeBtn.disabled = false;
                    mergeBtn.textContent = 'Merge PDFs';
                    return;
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);

                // Create a temporary download link and click it
                const a = document.createElement('a');
                a.href = url;
                a.download = 'merged.pdf';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                mergeBtn.textContent = 'Merge PDFs';
                mergeBtn.disabled = false;
                message.style.color = 'green';
                message.textContent = 'Merged PDF downloaded successfully!';
            } catch (err) {
                message.textContent = 'Error: ' + err.message;
                mergeBtn.disabled = false;
                mergeBtn.textContent = 'Merge PDFs';
            }
        });
    </script>

</body>

</html>