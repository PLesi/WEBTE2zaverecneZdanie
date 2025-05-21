<?php

session_start();
require_once 'config.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['login_status'] = "empty";
    header("Location: frontend/pages/login_form.php");
    exit();
}
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam("email", $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['login_status'] = "invalid";
        header("Location: frontend/pages/login_form.php");
        exit();
    }
    
    try {
        $stmt = $conn->prepare("SELECT api_key FROM api_keys WHERE user_id = :user_id");
        $stmt->bindParam("user_id", $user['id']);
        $stmt->execute();
        $api_key = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($api_key) {
            $_SESSION['api_key'] = $api_key['api_key'];
        } else {
            $_SESSION['api_key'] = null;
        }
    } catch (PDOException $e) {
        // If there's an error getting the API key, just continue
        // It's not a critical error that should prevent login
        $_SESSION['api_key'] = null;
    }

    // Verify password separately after we've already checked that user exists
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['login_status'] = "ok";
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = (bool)$user['is_admin']; // Set admin status in session

        header("Location: index.php");
        exit();
    } else {
        // Invalid password
        $_SESSION['login_status'] = "invalid";
        header("Location: frontend/pages/login_form.php");
        exit();
    }
} catch (PDOException $e) {
    // Log the error for server-side debugging (can be seen in error logs)
    error_log("Login error: " . $e->getMessage());
    
    // Set a generic error message for the user
    $_SESSION['login_status'] = "dbError";
    header("Location: frontend/pages/login_form.php");
    exit();
}