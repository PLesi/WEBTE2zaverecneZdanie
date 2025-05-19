<?php
$debugLogFile = '/tmp/my_api_key_debug.log';
error_log("DEBUG: Starting registration process", 3, $debugLogFile);

session_start();

require_once 'config.php';

error_log("DEBUG: Starting registration process", 3, $debugLogFile);

function generateApiKey(): string
{
    try {
        $randomBytes = random_bytes(32);
        $apiKey = bin2hex($randomBytes);
        error_log("DEBUG: API key generated: " . $apiKey, 3, $debugLogFile);

        return $apiKey;
    } catch (Exception $e) {
        error_log("Error generating API key: " . $e->getMessage());
        return "";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);         // repeat password
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);


    if (empty($username) || empty($email) || empty($password) || empty($password2)) {
        // v JS zoberieme "empty" a vypiseme hlasku v prislusnom jazyku
        $_SESSION['register_status'] = "empty";
        header("Location: ../pages/registration_form.php");
        exit();
    }

    if ($password != $password2) {
        $_SESSION['register_status'] = "passwordsNotEqual";
        header("Location: frontend/pages/registration_form.php");
        exit();
    }

    try {
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
        if ($apiKey == "") {
            $_SESSION["register_status"] = "apiKeyError";
            header("Location: frontend/pages/registration_form.php");
            exit();
        }
        error_log("DEBUG: Actual length of \$hasshed_apiKey: " . strlen($hasshed_apiKey), 3, $debugLogFile);
        error_log("DEBUG: Value of \$hasshed_apiKey: " . $hasshed_apiKey, 3, $debugLogFile);

        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
        $stmt->bindParam("email", $email);
        $stmt->bindParam("password", $hashed_password);
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $userId = $conn->lastInsertId();
        $stmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :api_key)");
        $stmt->bindParam("user_id", $userId);
        $stmt->bindParam("api_key", $hasshed_apiKey);
        $stmt->execute();

        $_SESSION["user_id"] = $userId;
        $_SESSION["api_key"] = $api_key;
        $_SESSION["register_status"] = "ok";
        $_SESSION["login_status"] = "ok";
        $_SESSION["username"] = $username;
        header("Location: index.php");                                                                                                                                                                                                                                                                                                                                                                                                                                                  
        exit();

    } catch (PDOException $e) {
        $_SESSION["register_status"] = "dbError";
        $_SESSION["reg_error"] = $e->getMessage();
        
        header("Location: frontend/pages/registration_form.php");
        exit();
    }
}