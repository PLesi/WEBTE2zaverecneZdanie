<?php
session_start();
$status = $_SESSION['login_status'] ?? '';
unset($_SESSION['login_status']);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="login.title">Prihlásenie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/styles.css" rel="stylesheet" />

</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" data-i18n="navbar.brand">PDF Editor</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="../pages/login_form.php" data-i18n="navbar.login">Prihlásenie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/registration_form.php" data-i18n="navbar.register">Registrácia</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('sk')">SK</button>
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('en')">EN</button>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-5 hero-section">
        <h2 data-i18n="login.heading">Prihlásenie</h2>

        <form id="loginForm" action="../../login.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label" data-i18n="login.label_email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required />
                <div id="emailError" class="text-danger mt-1" style="display: none;" data-i18n="login.error_invalid_email">Zadajte platný email.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label" data-i18n="login.label_password">Heslo:</label>
                <input type="password" name="password" id="password" class="form-control" required />
            </div>

            <?php if ($status): ?>
                <div class="text-danger mb-3" data-i18n="[html]login.error_<?php echo $status; ?>">
                    <?php
                    if ($status === "empty") {
                        echo i18next_t('login.error_empty');
                    } elseif ($status === "invalid") {
                        echo i18next_t('login.error_invalid');
                    } elseif ($status === "dbError") {
                        echo i18next_t('login.error_db');
                    }
                    ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100" data-i18n="login.submit_button">Prihlásiť sa</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- i18next -->
<script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
<!-- custom JS -->
<script src="../assets/js/i18n.js"></script>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailValue = emailInput.value.trim();

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(emailValue)) {
            e.preventDefault();
            emailError.style.display = 'block';
            emailInput.classList.add('is-invalid');
        } else {
            emailError.style.display = 'none';
            emailInput.classList.remove('is-invalid');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>