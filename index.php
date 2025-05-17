<?php
// Define base path for assets
$base_url = '/zz/WEBTE2zaverecneZdanie/'; // Adjust based on your server root
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Editor</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="frontend/assets/css/styles.css">
</head>
<body>
<!-- Navigačný panel -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" data-i18n="navbar.brand">PDF Editor</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" data-i18n="navbar.home">Domov</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-i18n="navbar.history">História</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-i18n="navbar.manual">Príručka</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-i18n="navbar.profile">Profil</a>
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

<!-- Hlavný obsah -->
<div class="hero-section">
    <div class="container">
        <h1 class="display-4" data-i18n="home.title">Vitajte v PDF Editore</h1>
        <p class="lead" data-i18n="home.description">Jednoducho spracujte svoje PDF súbory – spájajte, editujte, mažte stránky a viac.</p>
        <div class="row mt-5">
            <div class="col-md-4">
                <a href="<?php echo $base_url; ?>frontend/pages/compression.php" class="btn btn-primary operation-btn" data-i18n="operations.compress">Komprimovať PDF</a>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.jpg_to_pdf">JPG do PDF</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.merge">Spojiť PDF</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.rotate">Rotovať stránky</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.number">Číslovať stránky</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.protect">Pridať heslo</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.edit">Editovať PDF</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.delete_page">Odstrániť stránku</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.split">Rozdeliť PDF</button>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary operation-btn" data-i18n="operations.rearrange">Preskupiť stránky</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- Custom JS -->
<script src="frontend/assets/js/i18n.js"></script>
<script src="frontend/assets/js/main.js"></script>
</body>
</html>