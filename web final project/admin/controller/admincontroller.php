<?php
session_start();
include "../model/AdminModel.php";

// Only admin
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 1) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === "POST") {

    // Delete Route
    if (isset($_POST['delete_route_id'])) {
        $routeId = (int)$_POST['delete_route_id'];
        deleteRoute($routeId);
        header("Location: ../view/admindashboard.php?msg=route_deleted");
        exit();
    }

    // Delete Staff
    if (isset($_POST['delete_staff_mobile'])) {
        $mobile = trim($_POST['delete_staff_mobile']);
        deleteStaff($mobile);
        header("Location: ../view/admindashboard.php?msg=staff_deleted");
        exit();
    }

    // Add Route
    if (isset($_POST['add_route'])) {
        $from = trim($_POST['from_station'] ?? '');
        $to   = trim($_POST['to_station'] ?? '');
        $fare = (float)($_POST['fare'] ?? 0);

        if (empty($from) || empty($to) || $fare <= 0) {
            header("Location: ../view/admindashboard.php?error=invalid_route");
            exit();
        }

        if ($from === $to) {
            header("Location: ../view/admindashboard.php?error=same_stations");
            exit();
        }

        addRoute($from, $to, $fare);
        header("Location: ../view/admindashboard.php?msg=route_added");
        exit();
    }

    // Add Staff
    if (isset($_POST['add_staff'])) {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $mobile    = trim($_POST['mobile'] ?? '');
        $password  = $_POST['password'] ?? '';
        $roleId    = 2; // Staff role
        
        if (empty($firstName) || empty($mobile) || empty($password)) {
            header("Location: ../view/admindashboard.php?error=empty_fields");
            exit();
        }

        if (!preg_match('/^01[3-9]\d{8}$/', $mobile)) {
            header("Location: ../view/admindashboard.php?error=invalid_mobile");
            exit();
        }

        if (strlen($password) < 6) {
            header("Location: ../view/admindashboard.php?error=password_length");
            exit();
        }

        $result = addStaff($firstName, $lastName, $mobile, $password, $roleId);

        if ($result) {
            header("Location: ../view/admindashboard.php?msg=staff_added");
        } else {
            header("Location: ../view/admindashboard.php?error=add_staff_failed");
        }
        exit();
    }
}

header("Location: ../view/admindashboard.php");
exit();
?>
