<?php
session_start();
require_once('../Model/db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_pass     = $_POST['new_pass'] ?? '';
    $confirm_pass = $_POST['confirm_pass'] ?? '';
    $mobile       = $_SESSION['reset_mobile'] ?? '';

    if (empty($mobile)) {
        header("Location: ../View/php/forgot_password.php");
        exit();
    }

    if (empty($new_pass) || empty($confirm_pass)) {
        $_SESSION['error'] = "Both password fields are required!";
        header("Location: ../View/php/updatePass.php");
        exit();
    }

    if (strlen($new_pass) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters!";
        header("Location: ../View/php/updatePass.php");
        exit();
    }

    if ($new_pass !== $confirm_pass) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: ../View/php/updatePass.php");
        exit();
    }

    if (!$conn) {
        $_SESSION['error'] = "Database not connected. Please check configuration.";
        header("Location: ../View/php/updatePass.php");
        exit();
    }

    $hashedPass = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE Mobile = ?");
    $stmt->bind_param("ss", $hashedPass, $mobile);

    if ($stmt->execute()) {
        unset($_SESSION['reset_mobile']);
        header("Location: ../View/php/login.php?msg=PasswordUpdated");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update password. Please try again.";
        header("Location: ../View/php/updatePass.php");
        exit();
    }
} else {
    header("Location: ../View/php/updatePass.php");
    exit();
}
?>
