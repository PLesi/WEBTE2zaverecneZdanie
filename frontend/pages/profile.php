
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {
} else {
    // Ak nie je používateľ prihlásený, presmerujeme ho na prihlasovaciu stránku
    header("Location: login_form.php");
    exit();
}


require_once '../../config.php';

function generateApiKey(): string
{
    try {
        $randomBytes = random_bytes(32); // Generuje 32 kryptograficky bezpečných náhodných bajtov
        $apiKey = bin2hex($randomBytes); // Prevedie bajty na hexadecimálny reťazec (64 znakov)
        return $apiKey;
    } catch (Exception $e) {
        error_log("Chyba pri generovaní API kľúča: " . $e->getMessage());
        return ""; // V prípade chyby vráti prázdny reťazec
    }
}




$message = ""; // Správa pre používateľa
$username = "N/A"; // Meno používateľa z DB
$isAdmin = false;  // Admin status z DB
$currentDbApiKey = "N/A"; // API kľúč aktuálne uložený v DB
$sessionApiKey = $_SESSION['api_key'] ?? 'Chýba v session'; // API kľúč aktuálne v session

// --- Načítanie dát používateľa z DB pomocou ID zo session ---
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    try {
        $stmt = $conn->prepare("SELECT username, is_admin FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $fetchedUserData = $stmt->fetch();

        if ($fetchedUserData) {
            $username = htmlspecialchars($fetchedUserData['username']);
            $isAdmin = (bool)$fetchedUserData['is_admin']; // Konvertujeme TINYINT(1) na boolean
            $currentDbApiKey = "Potrebné načítať z api_keys tabuľky";
        } else {
            $message = "Chyba: Používateľ s ID {$userId} sa nenašiel v databáze. Vaša session môže byť neplatná.";
            session_destroy(); // Zničí session, ak používateľ neexistuje
            $userId = null; // Zruší user ID pre ďalšie spracovanie
            $sessionApiKey = 'Session vypršala / neplatná';
        }
    } catch (PDOException $e) {
        $message = "Chyba pri načítaní dát používateľa: " . $e->getMessage();
    }
} else {
    $message = "Chyba: ID používateľa nie je nastavené v session. Prosím, nastavte ho (simulované prihlásenie).";
}


// --- Spracovanie zmeny API kľúča (ak bol formulár odoslaný) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_api_key"]) && $userId) {
    $newApiKey = generateApiKey();

    if (!empty($newApiKey)) {
        try {
            // Aktualizácia API kľúča v tabuľke api_keys pre používateľa zisteného zo session
            $checkStmt = $conn->prepare("SELECT id FROM api_keys WHERE user_id = :user_id");
            $checkStmt->execute([':user_id' => $userId]);
            $existingKey = $checkStmt->fetch();
            
            if ($existingKey) {
                // Update existing API key
                $stmt = $conn->prepare("UPDATE api_keys SET api_key = :new_api_key WHERE user_id = :user_id");
                $result = $stmt->execute([
                    ':new_api_key' => $newApiKey,
                    ':user_id' => $userId
                ]);
            } else {
                // Insert new API key
                $stmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :new_api_key)");
                $result = $stmt->execute([
                    ':user_id' => $userId,
                    ':new_api_key' => $newApiKey
                ]);
            }

            if ($result) {
                // Aktualizácia API kľúča aj v session
                $_SESSION['api_key'] = $newApiKey;
                $currentDbApiKey = htmlspecialchars($newApiKey); // Aktualizujeme zobrazenú DB hodnotu
                $sessionApiKey = htmlspecialchars($newApiKey); // Aktualizujeme zobrazenú Session hodnotu
                $message = "API kľúč bol úspešne zmenený v DB aj v session!";
            } else {
                $message = "Chyba pri aktualizácii API kľúča v databáze (možno starý kľúč neexistuje?).";
            }
        } catch (PDOException $e) {
            $message = "Chyba DB pri zmene API kľúča: " . $e->getMessage();
        }
    } else {
        $message = "Chyba pri generovaní nového API kľúča.";
    }
}

?>

<?php
require_once '../../config.php';

$message = ""; // Správa pre používateľa
$username = "N/A"; // Meno používateľa z DB
$isAdmin = false;  // Admin status z DB
$currentDbApiKey = "N/A"; // API kľúč aktuálne uložený v DB
$sessionApiKey = $_SESSION['api_key'] ?? 'Chýba v session'; // API kľúč aktuálne v session
$userId = $_SESSION['user_id'] ?? null;

// Načítanie dát používateľa z DB pomocou ID zo session
if ($userId) {
    try {
        // Get user data from users table
        $stmt = $conn->prepare("SELECT username, is_admin FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $fetchedUserData = $stmt->fetch();

        // Get API key from api_keys table
        $apiKeyStmt = $conn->prepare("SELECT api_key FROM api_keys WHERE user_id = :user_id");
        $apiKeyStmt->execute([':user_id' => $userId]);
        $apiKeyData = $apiKeyStmt->fetch();

        if ($fetchedUserData) {
            $username = htmlspecialchars($fetchedUserData['username']);
            $isAdmin = (bool)$fetchedUserData['is_admin'];
            $currentDbApiKey = $apiKeyData ? htmlspecialchars($apiKeyData['api_key']) : "API kľúč nebol nájdený";
        } else {
            $message = "Chyba: Používateľ s ID {$userId} sa nenašiel v databáze.";
        }
    } catch (PDOException $e) {
        $message = "Chyba pri načítaní dát používateľa: " . $e->getMessage();
    }
}

// Spracovanie zmeny API kľúča (ak bol formulár odoslaný)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_api_key"]) && $userId) {
    try {
        // Generate a new API key
        $randomBytes = random_bytes(8);
        $newApiKey = bin2hex($randomBytes);
        
        // Check if user already has an API key in the api_keys table
        $checkStmt = $conn->prepare("SELECT id FROM api_keys WHERE user_id = :user_id");
        $checkStmt->execute([':user_id' => $userId]);
        $existingKey = $checkStmt->fetch();
        
        if ($existingKey) {
            // Update existing API key
            $updateStmt = $conn->prepare("UPDATE api_keys SET api_key = :api_key WHERE user_id = :user_id");
            $result = $updateStmt->execute([
                ':api_key' => $newApiKey,
                ':user_id' => $userId
            ]);
        } else {
            // Insert new API key
            $insertStmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :api_key)");
            $result = $insertStmt->execute([
                ':user_id' => $userId,
                ':api_key' => $newApiKey
            ]);
        }
        
        if (isset($result) && $result) {
            // Update session with new API key
            $_SESSION['api_key'] = $newApiKey;
            $currentDbApiKey = htmlspecialchars($newApiKey);
            $sessionApiKey = htmlspecialchars($newApiKey);
            $message = "API kľúč bol úspešne zmenený v DB aj v session!";
        } else {
            $message = "Chyba pri aktualizácii API kľúča v databáze.";
        }
    } catch (PDOException $e) {
        $message = "Chyba DB pri zmene API kľúča: " . $e->getMessage();
    } catch (Exception $e) {
        $message = "Chyba pri generovaní nového API kľúča: " . $e->getMessage();
    }
}

// Spracovanie admin kľúča
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_admin_key"]) && $userId && !$isAdmin) {
    $submittedAdminKey = trim($_POST["admin_key"]);
    
    try {
        // Získanie správneho admin kľúča z databázy
        $stmt = $conn->prepare("SELECT admin_key FROM admin_key LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result && $submittedAdminKey === $result['admin_key']) {
            // Ak kľúč súhlasí, aktualizujeme používateľa na admina
            $updateStmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE id = :id");
            $updateResult = $updateStmt->execute([':id' => $userId]);
            
            if ($updateResult) {
                $isAdmin = true;
                $_SESSION['is_admin'] = true; // Nastavíme admin status do session
                $message = "Gratulujeme! Boli ste povýšený na administrátora.";
            } else {
                $message = "Chyba pri aktualizácii admin práv.";
            }
        } else {
            $message = "Nesprávny admin kľúč. Prosím, skúste znova.";
        }
    } catch (PDOException $e) {
        $message = "Chyba pri overovaní admin kľúča: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správa API kľúča</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/css/styles.css" rel="stylesheet" />

    <style>
        h2, h3 {
            color: #f8f9fa;
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info p {
            margin: 10px 0;
            font-size: 1.1em;
            color: #f8f9fa;
        }
        .user-info strong {
            color: #adb5bd;
        }
        .admin-status {
            color: <?php echo $isAdmin ? '#28a745' : '#dc3545'; ?>;
            font-weight: bold;
        }
        .api-key-section {
            margin-top: 30px;
            border-top: 1px solid #495057;
            padding-top: 20px;
        }
        .api-key-section label {
            display: block;
            margin-bottom: 10px;
            color: #f8f9fa;
        }
        .api-key-display {
            background-color: #495057;
            padding: 10px 15px;
            border-radius: 4px;
            font-family: monospace;
            word-break: break-all;
            margin-bottom: 10px; /* Upravené, aby sa vmestili obe zobrazenia kľúčov */
            color: #adb5bd;
            font-size: 0.9em;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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
                        <a class="nav-link" href="manual.php" data-i18n="navbar.manual">Príručka</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" data-i18n="navbar.profile">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../logout.php" data-i18n="navbar.logout">Odhlásiť</a>
                    </li>
                    <li class="nav-item">
                        <button class="btn btn-outline-light ms-2" onclick="changeLanguage('sk')">SK</button>
                        <button class="btn btn-outline-light ms-2" onclick="changeLanguage('en')">EN</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-5 hero-section">
            <h2 data-i18n="profile.title">Správa API kľúča</h2>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'úspešne') !== false ? 'success' : 'error'; ?>">
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
            <?php endif; ?>

            <div class="user-info">
                <h3 data-i18n="profile.user_info_title">Vaše informácie</h3>
                <p><strong data-i18n="profile.username_label">Meno:</strong> <?php echo $username; ?></p>
                <?php if ($isAdmin): ?>
                <p><strong data-i18n="profile.status_label">Status:</strong> <span class="admin-status" data-i18n="profile.status_admin">Administrátor</span></p>
                <?php endif; ?>
            </div>

            <div class="api-key-section">
                <h2 data-i18n="profile.api_key_title">API kľúč</h2>
                <label for="dbApiKeyDisplay" data-i18n="profile.api_key_db_label">API kľúč uložený v databáze:</label>
                <div id="dbApiKeyDisplay" class="api-key-display">
                    <?php echo $currentDbApiKey; ?>
                </div>

                <label for="sessionApiKeyDisplay" data-i18n="profile.api_key_session_label">API kľúč uložený v session:</label>
                <div id="sessionApiKeyDisplay" class="api-key-display">
                    <?php echo $sessionApiKey; ?>
                </div>

                <form action="" method="post" class="api-key-actions">
                    <p data-i18n="profile.api_key_warning">Kliknite pre vygenerovanie nového API kľúča. Starý bude neplatný.</p>
                    <button class="btn btn-primary" type="submit" name="change_api_key" data-i18n="profile.generate_button">Vygenerovať a uložiť nový API kľúč</button>
                </form>
            </div>

            <?php if (!$isAdmin): ?>
                <div class="api-key-section">
                    <h2 data-i18n="admin_access.title">Admin prístup</h2>
                    <p data-i18n="admin_access.description" style="color: #f8f9fa; margin-bottom: 15px;">
                        Zadajte admin kľúč pre získanie admin privilégií:
                    </p>

                    <form action="" method="post" class="admin-key-form">
                        <div class="mb-3">
                            <input type="password"
                                   class="form-control"
                                   id="adminKey"
                                   name="admin_key"
                                   placeholder="Zadajte admin kľúč"
                                   style="background-color: #495057; color: #f8f9fa; border-color: #6c757d;"
                                   data-i18n-placeholder="admin_access.input_placeholder">
                        </div>
                        <button type="submit" name="submit_admin_key" class="btn btn-primary" data-i18n="admin_access.button">
                            Overiť kľúč
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- i18next -->
    <script src="https://unpkg.com/i18next@23.15.1/dist/umd/i18next.min.js"></script>
    <!-- custom JS -->
    <script src="../assets/js/i18n.js"></script>
</body>
</html>