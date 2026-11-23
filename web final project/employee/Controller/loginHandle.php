<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
require_once('../Model/db.php');

$mobile   = trim($_POST['userid'] ?? '');
$password = trim($_POST['password'] ?? '');

$userIdError = $passwordError = "";

if (empty($mobile)) {
    $userIdError = "Mobile number is required.";
} elseif (!preg_match('/^01[3-9]\d{8}$/', $mobile)) {
    $userIdError = "Invalid mobile number format (01XXXXXXXXX).";
}

if (empty($password)) {
    $passwordError = "Password is required.";
}

if ($userIdError || $passwordError) {
    $_SESSION['userIdError'] = $userIdError;
    $_SESSION['passwordError'] = $passwordError;
    $_SESSION['userIdValue'] = $mobile;
    header("Location: ../View/php/login.php");
    exit();
}

if (!$conn) {
    $_SESSION['userIdError'] = "Database not connected. Please verify database settings.";
    header("Location: ../View/php/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT UserID, RoleID, FirstName, LastName, Mobile, Password, Status FROM users WHERE Mobile = ?");
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $_SESSION['userIdError'] = "Mobile number not found.";
    header("Location: ../View/php/login.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

if ($user['Status'] === 'Blocked') {
    $_SESSION['userIdError'] = "Your account has been blocked. Please contact support.";
    header("Location: ../View/php/login.php");
    exit();
}

// Verify password with secure hash and legacy fallback
$passwordValid = password_verify($password, $user['Password']) || ($password === $user['Password']);

if (!$passwordValid) {
    $_SESSION['passwordError'] = "Incorrect password.";
    $_SESSION['userIdValue'] = $mobile;
    header("Location: ../View/php/login.php");
    exit();
}

// If password was stored in plaintext, auto-upgrade to secure bcrypt hash
if ($password === $user['Password'] && !password_verify($password, $user['Password'])) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upStmt = $conn->prepare("UPDATE users SET Password = ? WHERE UserID = ?");
    if ($upStmt) {
        $upStmt->bind_param("si", $newHash, $user['UserID']);
        $upStmt->execute();
        $upStmt->close();
    }
}

// Set session
$_SESSION['logged_in'] = true;
$_SESSION['UserID']    = (int)$user['UserID'];
$_SESSION['Mobile']    = $user['Mobile'];
$_SESSION['RoleID']    = (int)$user['RoleID'];
$_SESSION['UserName']  = trim($user['FirstName'] . ' ' . $user['LastName']);

// Role-based redirect
if ($user['RoleID'] == 1) {
    header("Location: ../../admin/view/admindashboard.php");
} elseif ($user['RoleID'] == 2) {
    header("Location: ../View/php/employeDashboard.php");
} elseif ($user['RoleID'] == 3) {
    header("Location: ../../passenger/view/dashboard_after_login.php");
} else {
    header("Location: ../View/php/login.php");
}
exit();
?>
