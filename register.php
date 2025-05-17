<?php
session_start();

require_once 'config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);         // repeat password
    $hashed_password = password_hash($password, PASSWORD_ARGON2I);

    if (empty($username) || empty($email) || empty($password) || empty($password2)) {
        // v JS zoberieme "empty" a vypiseme hlasku v prislusnom jazyku
        $_SESSION['register_status'] = "empty";
        header("Location: register.php");
        exit();
    }

    if ($password != $password2) {
        $_SESSION['register_status'] = "passwordsNotEqual";
        header("Location: register.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam("email", $email);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC) != null) {
            $_SESSION["register_error"] = "exist";
            header("Location: register.php");
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (:email, :username, :password)");
        $stmt->bindParam("email", $email);
        $stmt->bindParam("password", $hashed_password);
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $_SESSION["register_status"] = "ok";
    } catch (PDOException $e) {
        $_SESSION["register_status"] = "dbError";
        header("Location: register.php");
        exit();
    }
}