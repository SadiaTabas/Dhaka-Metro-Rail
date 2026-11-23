<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/Profile_Information.php";

if (!isset($_SESSION['UserID'], $_SESSION['RoleID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userId = (int)$_SESSION['UserID'];
$roleID = (int)$_SESSION['RoleID'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $fullName = trim($_POST['full_name'] ?? '');
    $dob      = trim($_POST['dob'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');

    if ($fullName === '') {
        header("Location: ../view/Profile_Information.php?msg=empty");
        exit();
    }

    $parts = explode(" ", $fullName, 2);
    $firstName = $parts[0];
    $lastName  = $parts[1] ?? '';

    $updateResult = updateUserProfile($userId, $firstName, $lastName, $dob, $gender);

    if ($updateResult) {
        $_SESSION['UserName'] = $fullName;
        if ($roleID === 1) {
            header("Location: ../view/admindashboard.php?msg=profile_updated");
        } elseif ($roleID === 2) {
            header("Location: ../../employee/View/php/employeDashboard.php?msg=profile_updated");
        } elseif ($roleID === 3) {
            header("Location: ../../passenger/view/dashboard_after_login.php?msg=profile_updated");
        } else {
            header("Location: ../../employee/View/php/login.php");
        }
        exit();
    } else {
        header("Location: ../view/Profile_Information.php?msg=error");
        exit();
    }
}

header("Location: ../view/Profile_Information.php");
exit();
?>
