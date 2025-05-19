<?php
// Define debug log file at the very top, before any other operations
$debugLogFile = '/tmp/my_api_keys_debug.log';

// --- LOGGING POINT 1: Script started ---
error_log("DEBUG: register.php script STARTED execution. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);

session_start();

// --- LOGGING POINT 2: After session_start ---
error_log("DEBUG: session_start() executed. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);

require_once 'config.php';

// --- LOGGING POINT 3: After config.php loaded ---
error_log("DEBUG: config.php required. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);

// Ensure $conn is available here. If config.php has issues, the script might die here.
if ($conn === null) {
    error_log("FATAL DEBUG: \$conn is NULL after requiring config.php. Database connection likely failed.", 3, $debugLogFile);
    // Optionally, you might want to die here to prevent further errors if the connection is critical
    // die("Critical error: Database connection not established.");
}


function generateApiKey(): string
{
    // Make sure $debugLogFile is accessible inside the function if you want to use it
    // The safest way is to pass it as an argument or make it global (which you did by defining it outside)
    global $debugLogFile; // Explicitly declare it global for clarity, though not strictly needed here due to file scope

    try {
        $randomBytes = random_bytes(32);
        $apiKey = bin2hex($randomBytes);
        error_log("DEBUG: API key generated inside function: " . $apiKey . " (Length: " . strlen($apiKey) . ")", 3, $debugLogFile);
        return $apiKey;
    } catch (Exception $e) {
        // --- LOGGING POINT: Error in API key generation ---
        error_log("Error generating API key: " . $e->getMessage() . " (File: " . __FILE__ . ", Line: " . __LINE__ . ")", 3, $debugLogFile);
        return "";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- LOGGING POINT 4: POST request received ---
    error_log("DEBUG: POST request received. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);         // repeat password
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);

    // ... (rest of your existing validation and database logic) ...

    try {
        // --- LOGGING POINT 5: Entering try block for DB operations ---
        error_log("DEBUG: Entering try block for DB operations. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);

        $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam("email", $email);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC) != null) {
            $_SESSION["register_status"] = "exist";
            header("Location: frontend/pages/registration_form.php");
            exit();
        }

        $apiKey = generateApiKey();
        $hasshed_apiKey = password_hash($apiKey, PASSWORD_ARGON2I);

        // --- LOGGING POINT 6: Hashed API Key details ---
        error_log("DEBUG: Actual length of \$hasshed_apiKey: " . strlen($hasshed_apiKey), 3, $debugLogFile);
        error_log("DEBUG: Value of \$hasshed_apiKey: " . $hasshed_apiKey, 3, $debugLogFile);
        // ---------------------------------------------------------
        $stmt = $conn->prepare("SHOW CREATE TABLE api_keys");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $createTableStatement = $result['Create Table'];
        error_log("DEBUG: CREATE TABLE statement for api_keys: " . $createTableStatement, 3, $debugLogFile);
        // ---------------------------------------------------------
        if ($apiKey == "") {
            $_SESSION["register_status"] = "apiKeyError";
            header("Location: frontend/pages/registration_form.php");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
        $stmt->bindParam("email", $email);
        $stmt->bindParam("password", $hashed_password);
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $userId = $conn->lastInsertId();

        // --- LOGGING POINT 7: User inserted, attempting API key insert ---
        error_log("DEBUG: User inserted (ID: " . $userId . "). Attempting API key insert. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);


        $stmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :api_key)");
        $stmt->bindParam("user_id", $userId);
        $stmt->bindParam("api_key", $hasshed_apiKey);
        $stmt->execute();

        // --- LOGGING POINT 8: API Key inserted successfully ---
        error_log("DEBUG: API Key inserted successfully. Registration complete. Timestamp: " . date('Y-m-d H:i:s') . "\n", 3, $debugLogFile);


        $_SESSION["user_id"] = $userId;
        $_SESSION["api_key"] = $apiKey;
        $_SESSION["register_status"] = "ok";
        $_SESSION["login_status"] = "ok";
        $_SESSION["username"] = $username;
        header("Location: index.php");                                                                                                                                                                                                                                                                                                                                                                                                                                                  
        exit();

    } catch (PDOException $e) {
        // --- LOGGING POINT 9: Database error caught ---
        error_log("DEBUG: Database Error during registration: ". $e->getMessage() . " (File: " . $e->getFile() . ", Line: " . $e->getLine() . ")", 3, $debugLogFile);
        $_SESSION["register_status"] = "dbError";
        $_SESSION["reg_error"] = $e->getMessage();
        
        header("Location: frontend/pages/registration_form.php");
        exit();
    }
} else {
    // --- LOGGING POINT: Not a POST request ---
    error_log("DEBUG: Not a POST request to register.php. Method: " . $_SERVER["REQUEST_METHOD"] . "\n", 3, $debugLogFile);
}