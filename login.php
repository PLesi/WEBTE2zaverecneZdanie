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

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['login_status'] = "ok";
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