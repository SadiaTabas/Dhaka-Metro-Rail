<?php
session_start();
include "../model/ChangePasswordModel.php";

if (!isset($_SESSION['Mobile'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$mobile = trim($_SESSION['Mobile']);

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $currentPass = $_POST['current_password'] ?? '';
    $newPass     = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
        header("Location: ../view/changepassword.php?msg=empty");
        exit();
    }

    if (strlen($newPass) < 6) {
        header("Location: ../view/changepassword.php?msg=length");
        exit();
    }

    if ($newPass !== $confirmPass) {
        header("Location: ../view/changepassword.php?msg=nomatch");
        exit();
    }

    $userRow = getPasswordByMobile($mobile);

    if (!$userRow) {
        header("Location: ../view/changepassword.php?msg=usernotfound");
        exit();
    }

    // Verify current password (supports hash & legacy plaintext)
    $matches = password_verify($currentPass, $userRow['Password']) || ($currentPass === $userRow['Password']);

    if (!$matches) {
        header("Location: ../view/changepassword.php?msg=wrong");
        exit();
    }

    if (updatePasswordByMobile($mobile, $newPass)) {
        $roleID = (int)($_SESSION['RoleID'] ?? 3);
        if ($roleID === 1) {
            header("Location: ../view/admindashboard.php?msg=password_updated");
        } elseif ($roleID === 2) {
            header("Location: ../../employee/View/php/employeDashboard.php?msg=password_updated");
        } elseif ($roleID === 3) {
            header("Location: ../../passenger/view/dashboard_after_login.php?msg=password_updated");
        } else {
            header("Location: ../../employee/View/php/login.php?msg=password_updated");
        }
        exit();
    } else {
        header("Location: ../view/changepassword.php?msg=error");
        exit();
    }
} else {
    header("Location: ../view/changepassword.php");
    exit();
}
?>
