<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

function generateApiKey(): string
{
    try {
        $randomBytes = random_bytes(8);
        $apiKey = bin2hex($randomBytes);
        return $apiKey;
    } catch (Exception $e) {
        error_log("API Key generation error: " . $e->getMessage());
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

        // Check if the database connection is valid
        if (!$conn) {
            error_log("Database connection is not available");
            $_SESSION["register_status"] = "dbError";
            $_SESSION["reg_error"] = "Database connection failed";
            header("Location: frontend/pages/registration_form.php");
            exit();
        }

        // Insert user data with try/catch for each query
        try {
            $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
            $stmt->bindParam("email", $email);
            $stmt->bindParam("password", $hashed_password);
            $stmt->bindParam("username", $username);
            $stmt->execute();
            $userId = $conn->lastInsertId();
            
            if (!$userId) {
                throw new PDOException("Failed to get last insert ID");
            }
            
            error_log("User inserted successfully with ID: " . $userId);
        } catch (PDOException $userInsertError) {
            error_log("Error inserting user: " . $userInsertError->getMessage());
            throw $userInsertError; // Re-throw to be caught by the outer catch block
        }

        // Insert API key with separate try/catch
        try {
            $stmt = $conn->prepare("INSERT INTO api_keys (user_id, api_key) VALUES (:user_id, :api_key)");
            $stmt->bindParam("user_id", $userId);
            $stmt->bindParam("api_key", $apiKey);
            $stmt->execute();
            error_log("API key inserted successfully for user ID: " . $userId);
        } catch (PDOException $apiKeyInsertError) {
            error_log("Error inserting API key: " . $apiKeyInsertError->getMessage());
            throw $apiKeyInsertError; // Re-throw to be caught by the outer catch block
        }

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
        
        // Log the error with detailed information for debugging
        error_log("Registration PDO error: " . $e->getMessage());
        error_log("Error code: " . $e->getCode());
        error_log("SQL state: " . $e->errorInfo[0] ?? 'Unknown');
        
        header("Location: frontend/pages/registration_form.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} 