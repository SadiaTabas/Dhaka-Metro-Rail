<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
require_once('../Model/db.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = trim($_POST['phone'] ?? '');

    if (empty($phone)) {
        $_SESSION['error'] = "Mobile number is required.";
        header("Location: ../View/php/forgot_password.php");
        exit();
    }

    if (!preg_match('/^01[3-9]\d{8}$/', $phone)) {
        $_SESSION['error'] = "Invalid mobile number format.";
        header("Location: ../View/php/forgot_password.php");
        exit();
    }

    if (!$conn) {
        $_SESSION['error'] = !empty($GLOBALS['db_connection_error'])
            ? "Database unavailable: " . $GLOBALS['db_connection_error']
            : "Database connection failed. Please verify database connection settings.";
        header("Location: ../View/php/forgot_password.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT UserID FROM users WHERE Mobile = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['reset_mobile'] = $phone;
        header("Location: ../View/php/updatePass.php");
        exit();
    } else {
        $_SESSION['error'] = "Mobile number not found in our records.";
        header("Location: ../View/php/forgot_password.php");
        exit();
    }
} else {
    header("Location: ../View/php/forgot_password.php");
    exit();
}
?>
