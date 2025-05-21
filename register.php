<?php
session_start();
require_once 'config.php';
function generateApiKey(): string
{
    try {
        $randomBytes = random_bytes(8);
        $apiKey = bin2hex($randomBytes);
        return $apiKey;
    } catch (Exception $e) {
        return "";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);         // repeat password
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);


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

        $stmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :api_key)");
        $stmt->bindParam("user_id", $userId);
        $stmt->bindParam("api_key", $apiKey);
        $stmt->execute();

        $_SESSION["user_id"] = $userId;
        $_SESSION["api_key"] = $apiKey;
        $_SESSION["register_status"] = "ok";
        $_SESSION["login_status"] = "ok";
        $_SESSION["logged_in"] = true;
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