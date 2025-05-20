
<?php
/*   NEMAZAŤ
session_start(); 

require_once 'config.php';

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
        $stmt = $pdo->prepare("SELECT username, is_admin, api_key FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $fetchedUserData = $stmt->fetch();

        if ($fetchedUserData) {
            $username = htmlspecialchars($fetchedUserData['username']);
            $isAdmin = (bool)$fetchedUserData['is_admin']; // Konvertujeme TINYINT(1) na boolean
            $currentDbApiKey = htmlspecialchars($fetchedUserData['api_key']);
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
            // Aktualizácia API kľúča v databáze pre používateľa zisteného zo session
            $stmt = $pdo->prepare("UPDATE users SET api_key = :new_api_key WHERE id = :id");
            $result = $stmt->execute([
                ':new_api_key' => $newApiKey,
                ':id' => $userId
            ]);

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
*/
?>

<?php
$message = ""; // Správa pre používateľa
$username = "N/A"; // Meno používateľa z DB
$isAdmin = false;  // Admin status z DB
$currentDbApiKey = "N/A"; // API kľúč aktuálne uložený v DB
$sessionApiKey = $_SESSION['api_key'] ?? 'Chýba v session'; // API kľúč aktuálne v session
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
        .api-key-actions button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            margin-top: 15px;
        }
        .api-key-actions button:hover {
            background-color: #0056b3;
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
    <?php include 'navbar.php'; ?>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-5 hero-section">
            <h2>Správa API kľúča</h2>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'úspešne') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="user-info">
                <h3>Vaše informácie</h3>
                <p><strong>ID Používateľa (zo Session):</strong> <?php echo htmlspecialchars($userId ?? 'N/A'); ?></p>
                <p><strong>Meno (z DB):</strong> <?php echo $username; ?></p>
                <p><strong>Status (z DB):</strong> <span class="admin-status"><?php echo $isAdmin ? 'Administrátor' : 'Bežný užívateľ'; ?></span></p>
            </div>

            <div class="api-key-section">
                <h2>API kľúč</h2>
                <label for="dbApiKeyDisplay">API kľúč uložený v databáze:</label>
                <div id="dbApiKeyDisplay" class="api-key-display">
                    <?php echo $currentDbApiKey; ?>
                </div>

                <label for="sessionApiKeyDisplay">API kľúč uložený v session:</label>
                <div id="sessionApiKeyDisplay" class="api-key-display">
                    <?php echo $sessionApiKey; ?>
                </div>

                <form action="" method="post" class="api-key-actions">
                    <p>Kliknite pre vygenerovanie nového API kľúča. Starý bude neplatný.</p>
                    <button type="submit" name="change_api_key">Vygenerovať a uložiť nový API kľúč</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>