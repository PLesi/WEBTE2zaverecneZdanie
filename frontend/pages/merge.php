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
    <title data-i18n="merge.title">Spojiť PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <link href="../assets/css/operations.css" rel="stylesheet">
</head>

<body>
<!-- Navigačný panel -->
<?php include 'navbarPDFoperations.php'; ?>
<div class="hero-section">
    <div class="container mt-5">
        <h1 class="display-4" data-i18n="merge.title">Spojiť dva PDF súbory</h1>

        <!-- Chybové hlásenie -->
        <div id="errorMessage">
            <span id="errorText"></span>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <!-- Skrytý input pre prvý súbor -->
                    <input type="file" id="file1" accept="application/pdf" />

                    <!-- Vlastné tlačidlo s ikonou pre prvý súbor -->
                    <label for="file1" class="custom-file-upload">
                        <i class="bi bi-upload"></i>
                        <span data-i18n="merge.label_file1">Vyberte prvý PDF</span>
                    </label>

                    <!-- Zobrazenie názvu prvého súboru -->
                    <div id="fileInfo1" class="mt-2">
                        <span id="fileNameDisplay1"></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-center mb-3">
                    <!-- Skrytý input pre druhý súbor -->
                    <input type="file" id="file2" accept="application/pdf" />

                    <!-- Vlastné tlačidlo s ikonou pre druhý súbor -->
                    <label for="file2" class="custom-file-upload">
                        <i class="bi bi-upload"></i>
                        <span data-i18n="merge.label_file2">Vyberte druhý PDF</span>
                    </label>

                    <!-- Zobrazenie názvu druhého súboru -->
                    <div id="fileInfo2" class="mt-2">
                        <span id="fileNameDisplay2"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button id="mergeBtn" class="btn btn-primary" data-i18n="merge.merge_button">Spojiť PDF</button>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS -->
<script src="../assets/js/i18n.js"></script>

<script>
    const mergeBtn = document.getElementById('mergeBtn');
    const fileInput1 = document.getElementById('file1');
    const fileInput2 = document.getElementById('file2');
    const fileNameDisplay1 = document.getElementById('fileNameDisplay1');
    const fileNameDisplay2 = document.getElementById('fileNameDisplay2');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

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

    // Spracovanie prvého súboru
    fileInput1.addEventListener('change', () => {
        const file = fileInput1.files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                fileNameDisplay1.textContent = '';
                showErrorMessage(i18next.t('merge.error_invalid_file'));
                fileInput1.value = ''; // Reset inputu
            } else {
                fileNameDisplay1.textContent = file.name;
            }
        } else {
            fileNameDisplay1.textContent = '';
        }
    });

    // Spracovanie druhého súboru
    fileInput2.addEventListener('change', () => {
        const file = fileInput2.files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                fileNameDisplay2.textContent = '';
                showErrorMessage(i18next.t('merge.error_invalid_file'));
                fileInput2.value = ''; // Reset inputu
            } else {
                fileNameDisplay2.textContent = file.name;
            }
        } else {
            fileNameDisplay2.textContent = '';
        }
    });

    mergeBtn.addEventListener('click', async () => {
        const file1 = fileInput1.files[0];
        const file2 = fileInput2.files[0];

        if (!file1 || !file2) {
            showErrorMessage(i18next.t('merge.error_no_files'));
            return;
        }

        if (file1.type !== 'application/pdf' || file2.type !== 'application/pdf') {
            showErrorMessage(i18next.t('merge.error_invalid_file'));
            return;
        }

        const formData = new FormData();
        formData.append('file1', file1);
        formData.append('file2', file2);
        formData.append('apiKey', 'asd');
        formData.append('platform', 'frontend');

        try {
            mergeBtn.disabled = true;
            mergeBtn.textContent = i18next.t('merge.merging');

            const response = await fetch('http://node75.webte.fei.stuba.sk/api/pdf/merge', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                showErrorMessage(i18next.t('merge.error_merge', { error: errorText }));
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

            showErrorMessage(i18next.t('merge.success_message'));

        } catch (err) {
            showErrorMessage(i18next.t('merge.error_failed', { error: err.message }));
        } finally {
            mergeBtn.disabled = false;
            mergeBtn.textContent = i18next.t('merge.merge_button');
        }
    });
</script>
</body>

</html>