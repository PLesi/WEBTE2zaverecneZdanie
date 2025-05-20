<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download as PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--Custom CSS -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" data-i18n="navbar.brand">PDF Editor</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php" data-i18n="navbar.home">Domov</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php" data-i18n="navbar.history">História</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" data-i18n="navbar.manual">Príručka</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php" data-i18n="navbar.profile">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-i18n="navbar.logout">Odhlásiť</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-light ms-2" onclick="changeLanguage('sk')">SK</button>
                        <button class="btn btn-outline-light ms-2" onclick="changeLanguage('en')">EN</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- To co bude v PDF, taktiež viditelne na webe -->
    <div id="toPDF" style="border: 1px solid #ccc; padding: 20px;">
        <h1>PDF Editor manual</h1>
        <p>Operácie:</p>
        <ul>
            <li>Komprimovať PDF</li>
            <li>JPG do PDF</li>
            <li>Spojiť PDF</li>
            <li>Rotovať stránky</li>
            <li>Číslovať stránky</li>
            <li>Pridať heslo</li>
            <li>Editovať PDF</li>
            <li>Odstrániť stránku</li>
            <li>Rozdeliť PDF</li>
            <li>Preskupiť stránky</li>
        </ul>
        <h2>1. Komprimovať PDF</h2>
        <p>Na kompresiu PDF súboru vyberte súbor a nastavte úroveň kompresie (1-9).</p>
        <h2>2. JPG do PDF</h2>
        <p>Na prevod JPG obrázkov do PDF vyberte obrázky a nastavte poradie.</p>
        <h2>3. Spojiť PDF</h2>
        <p>Na spojenie viacerých PDF súborov vyberte súbory a nastavte poradie.</p>
        <h2>4. Rotovať stránky</h2>
        <p>Na otočenie stránok PDF súboru vyberte súbor a nastavte uhol rotácie.</p>
        <h2>5. Číslovať stránky</h2>
        <p>Na číslovanie stránok PDF súboru vyberte súbor a nastavte formát číslovania.</p>
        <h2>6. Pridať heslo</h2>
        <p>Na pridanie hesla do PDF súboru vyberte súbor a zadajte heslo.</p>
        <h2>7. Editovať PDF</h2>
        <p>Na editáciu PDF súboru vyberte súbor a nastavte požadované úpravy.</p>
        <h2>8. Odstrániť stránku</h2>
        <p>Na odstránenie stránky z PDF súboru vyberte súbor a nastavte číslo stránky na odstránenie.</p>
        <h2>9. Rozdeliť PDF</h2>
        <p>Na rozdelenie PDF súboru vyberte súbor a nastavte rozsah stránok na rozdelenie.</p>
        <h2>10. Preskupiť stránky</h2>
        <p>Na preskupenie stránok PDF súboru vyberte súbor a nastavte nové poradie stránok.</p>
    </div>

    <button onclick="downloadPDF()">Download as PDF</button>

    <script>
        function downloadPDF() {
            const pdfContent = document.getElementById('toPDF').innerHTML;
            const formData = new FormData();
            formData.append('pdf_content', pdfContent);

            fetch('../../manualPDF.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'manual.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>

</body>
</html>