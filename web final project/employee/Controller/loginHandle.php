<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
require_once('../Model/db.php');

$rawInput = trim($_POST['userid'] ?? '');
$password = trim($_POST['password'] ?? '');

$userIdError = $passwordError = "";

// Normalize input: strip spaces, dashes, parentheses
$cleanInput = preg_replace('/[\s\-\(\)]+/', '', $rawInput);
if (strpos($cleanInput, '+88') === 0) {
    $cleanInput = substr($cleanInput, 3);
} elseif (strpos($cleanInput, '880') === 0) {
    $cleanInput = substr($cleanInput, 2);
}

$isMobile = (bool)preg_match('/^01[3-9]\d{8}$/', $cleanInput);
$isUserId = (bool)(is_numeric($cleanInput) && strlen($cleanInput) <= 8 && $cleanInput > 0);

if (empty($rawInput)) {
    $userIdError = "Registered mobile number or User ID is required.";
} elseif (!$isMobile && !$isUserId) {
    $userIdError = "Please enter a valid 11-digit mobile (01XXXXXXXXX) or numeric User ID.";
}

if (empty($password)) {
    $passwordError = "Password is required.";
}

if ($userIdError || $passwordError) {
    $_SESSION['userIdError'] = $userIdError;
    $_SESSION['passwordError'] = $passwordError;
    $_SESSION['userIdValue'] = $rawInput;
    header("Location: ../View/php/login.php");
    exit();
}

if (!$conn) {
    $_SESSION['userIdError'] = "Database temporarily unavailable. Please verify connection.";
    $_SESSION['userIdValue'] = $rawInput;
    header("Location: ../View/php/login.php");
    exit();
}

// Query user by Mobile or UserID
if ($isMobile) {
    $stmt = $conn->prepare("SELECT UserID, RoleID, FirstName, LastName, Mobile, Password, Status FROM users WHERE Mobile = ?");
    $stmt->bind_param("s", $cleanInput);
} else {
    $uid = (int)$cleanInput;
    $stmt = $conn->prepare("SELECT UserID, RoleID, FirstName, LastName, Mobile, Password, Status FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $uid);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {
    $_SESSION['userIdError'] = "User not found in database. Please check your credentials or create an account.";
    $_SESSION['userIdValue'] = $rawInput;
    header("Location: ../View/php/login.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

if ($user['Status'] === 'Blocked') {
    $_SESSION['userIdError'] = "Your account has been blocked. Please contact Dhaka Metro Rail administration.";
    header("Location: ../View/php/login.php");
    exit();
}

// Verify password with secure hash and legacy fallback
$passwordValid = password_verify($password, $user['Password']) || ($password === $user['Password']);

if (!$passwordValid) {
    $_SESSION['passwordError'] = "Incorrect password. Please verify and try again.";
    $_SESSION['userIdValue'] = $rawInput;
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

// Regenerate session ID on successful login to prevent session fixation attacks
session_regenerate_id(true);

$_SESSION['logged_in'] = true;
$_SESSION['UserID']    = (int)$user['UserID'];
$_SESSION['Mobile']    = $user['Mobile'];
$_SESSION['RoleID']    = (int)$user['RoleID'];
$_SESSION['UserName']  = trim($user['FirstName'] . ' ' . $user['LastName']);

// Role-based redirect to respective dashboard
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
