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
    $stmt = $connm->prepare("SELECT api_key FROM api_keys WHERE user_id = :user_id");
    $stmt->bindParam("user_id", $user['id']);
    $stmt->execute();
    $api_key = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($api_key) {
        $_SESSION['api_key'] = $api_key['api_key'];
    } else {
        $_SESSION['api_key'] = null;
    }

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_status'] = "ok";
        $_SESSION['logged_in'] = true;

        header("Location: index.php");
        exit();
    } else {
        $_SESSION['login_status'] = "invalid";
        header("Location: frontend/pages/login_form.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['login_status'] = "dbError";
    header("Location: frontend/pages/login_form.php");
    exit();
}