<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/UserModel.php";

$fullname = trim($_POST['fullname'] ?? '');
$rawMobile = trim($_POST['mobile'] ?? '');
$nid      = trim($_POST['nid'] ?? '');
$dob      = trim($_POST['dob'] ?? '');
$gender   = trim($_POST['gender'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

// Normalize mobile number
$mobile = preg_replace('/[\s\-\(\)]+/', '', $rawMobile);
if (strpos($mobile, '+88') === 0) {
    $mobile = substr($mobile, 3);
} elseif (strpos($mobile, '880') === 0) {
    $mobile = substr($mobile, 2);
}

if (
    empty($fullname) || empty($mobile) || empty($nid) ||
    empty($dob) || empty($gender) ||
    empty($password) || empty($confirm)
) {
    $_SESSION['signup_error'] = "All fields are required.";
    header("Location: ../view/signup.php");
    exit();
}

if (!preg_match('/^01[3-9]\d{8}$/', $mobile)) {
    $_SESSION['signup_error'] = "Mobile must be a valid 11-digit Bangladeshi number (01XXXXXXXXX).";
    header("Location: ../view/signup.php");
    exit();
}

if (!is_numeric($nid) || strlen($nid) < 10) {
    $_SESSION['signup_error'] = "NID must be a valid number of at least 10 digits.";
    header("Location: ../view/signup.php");
    exit();
}

if ($password !== $confirm) {
    $_SESSION['signup_error'] = "Passwords do not match.";
    header("Location: ../view/signup.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['signup_error'] = "Password must be at least 6 characters.";
    header("Location: ../view/signup.php");
    exit();
}

$name = explode(" ", $fullname, 2);
$firstName = $name[0];
$lastName  = $name[1] ?? "";

$result = createUser(
    $firstName,
    $lastName,
    $mobile,
    $nid,
    $dob,
    $gender,
    $password
);

if ($result === "exists") {
    $_SESSION['signup_error'] = "An account with this mobile number already exists.";
    header("Location: ../view/signup.php");
    exit();
} elseif ($result === true) {
    $_SESSION['signup_success'] = "Account created successfully! Please login.";
    header("Location: ../../employee/View/php/login.php?msg=registered");
    exit();
} else {
    $_SESSION['signup_error'] = "Registration failed. Please check database connection.";
    header("Location: ../view/signup.php");
    exit();
}
?>
