<?php
session_start();
$status = $_SESSION['register_status'] ?? '';
$error = $_SESSION['reg_error'] ?? '';
unset($_SESSION['register_status']);

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: ../../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title data-i18n="register.title">Registrácia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/styles.css" rel="stylesheet" />
</head>
<body>

<!-- Navbar začiatok -->
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
                    <a class="nav-link" href="../pages/login_form.php" data-i18n="navbar.login">Prihlásenie</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="../pages/registration_form.php" data-i18n="navbar.register">Registrácia</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('sk')">SK</button>
                    <button class="btn btn-outline-light ms-2" onclick="changeLanguage('en')">EN</button>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- Navbar koniec -->

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-5 hero-section">
        <h2 data-i18n="register.heading">Registrácia</h2>

        <form id="registerForm" action="../../register.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label" data-i18n="register.label_username">Meno:</label>
                <input type="text" id="username" name="username" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label" data-i18n="register.label_email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required />
                <div id="emailError" class="text-danger mt-1" style="display:none;" data-i18n="register.error_invalid_email">Zadajte platný email.</div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label" data-i18n="register.label_password">Heslo:</label>
                <input type="password" id="password" name="password" class="form-control" required />
            </div>

            <div class="mb-3">
                <label for="password2" class="form-label" data-i18n="register.label_confirm_password">Potvrď heslo:</label>
                <input type="password" id="password2" name="password2" class="form-control" required />
                <div id="passwordError" class="text-danger mt-1" style="display:none;" data-i18n="register.error_password_mismatch">Heslá sa nezhodujú.</div>
            </div>

            <?php if ($status): ?>
                <div class="text-danger mb-3">
                    <?php
                    if($error){
                        echo "Chyba: " . htmlspecialchars($error);
                    }

                    if ($status === "empty") {
                        echo "Vyplňte všetky polia.";
                    } elseif ($status === "passwordsNotEqual") {
                        echo "Heslá sa nezhodujú.";
                    } elseif ($status === "exist") {
                        echo "Tento email je už zaregistrovaný.";
                    } elseif ($status === "apiKeyError") {
                        echo "Chyba pri generovaní API kľúča. Skúste znova.";
                    } elseif ($status === "dbError") {
                        echo "Chyba databázy. Skúste znova neskôr.";
                    }
                    ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary w-100" data-i18n="register.submit_button">Registrovať sa</button>
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
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let valid = true;

        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            emailError.style.display = 'block';
            emailInput.classList.add('is-invalid');
            valid = false;
        } else {
            emailError.style.display = 'none';
            emailInput.classList.remove('is-invalid');
        }

        const password = document.getElementById('password').value;
        const password2 = document.getElementById('password2').value;
        const passwordError = document.getElementById('passwordError');
        if (password !== password2) {
            passwordError.style.display = 'block';
            valid = false;
        } else {
            passwordError.style.display = 'none';
        }

        if (!valid) e.preventDefault();
    });

    // Funkcia pre PHP kompatibilitu s i18next (simulácia)
    function i18next_t(key, options = {}) {
        return i18next.t(key, options);
    }
</script>
</body>
</html>