<?php
session_start();
include "../Model/EmployeeModel.php";

if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: ../View/php/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Station Management
    if (isset($_POST['add_station'])) {
        $name = trim($_POST['station_name'] ?? '');
        if (!empty($name)) addStation($name);
        header("Location: ../View/php/employeDashboard.php?msg=station_added");
        exit();
    } 
    elseif (isset($_POST['update_station'])) {
        $id = (int)($_POST['station_id'] ?? 0);
        $name = trim($_POST['station_name'] ?? '');
        if (!empty($name) && $id > 0) updateStation($id, $name);
        header("Location: ../View/php/employeDashboard.php?msg=station_updated");
        exit();
    }
    elseif (isset($_POST['delete_station'])) {
        $id = (int)($_POST['station_id'] ?? 0);
        if ($id > 0) deleteStation($id);
        header("Location: ../View/php/employeDashboard.php?msg=station_deleted");
        exit();
    }

    // Schedule Management
    elseif (isset($_POST['add_schedule'])) {
        $trainName = trim($_POST['train_name'] ?? '');
        $routeId   = (int)($_POST['route_id'] ?? 0);
        $depTime   = trim($_POST['dep_time'] ?? '');
        $arrTime   = trim($_POST['arr_time'] ?? '');
        if (!empty($trainName) && $routeId > 0 && !empty($depTime) && !empty($arrTime)) {
            addSchedule($trainName, $routeId, $depTime, $arrTime);
        }
        header("Location: ../View/php/employeDashboard.php?msg=schedule_added");
        exit();
    }
    elseif (isset($_POST['update_schedule'])) {
        $id        = (int)($_POST['schedule_id'] ?? 0);
        $trainName = trim($_POST['train_name'] ?? '');
        $routeId   = (int)($_POST['route_id'] ?? 0);
        $depTime   = trim($_POST['dep_time'] ?? '');
        $arrTime   = trim($_POST['arr_time'] ?? '');
        if ($id > 0 && !empty($trainName) && $routeId > 0 && !empty($depTime) && !empty($arrTime)) {
            updateSchedule($id, $trainName, $routeId, $depTime, $arrTime);
        }
        header("Location: ../View/php/employeDashboard.php?msg=schedule_updated");
        exit();
    }
    elseif (isset($_POST['delete_schedule'])) {
        $id = (int)($_POST['schedule_id'] ?? 0);
        if ($id > 0) deleteSchedule($id);
        header("Location: ../View/php/employeDashboard.php?msg=schedule_deleted");
        exit();
    }

    // Ticket Validation
    elseif (isset($_POST['add_validation'])) {
        $ticketId    = (int)($_POST['ticket_id'] ?? 0);
        $status      = trim($_POST['status'] ?? 'Valid');
        $validatedBy = (int)$_SESSION['UserID'];
        if ($ticketId > 0) {
            addValidation($ticketId, $status, $validatedBy);
        }
        header("Location: ../View/php/employeDashboard.php?msg=validation_added");
        exit();
    }
    elseif (isset($_POST['update_validation'])) {
        $id     = (int)($_POST['validation_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Valid');
        if ($id > 0) {
            updateValidationStatus($id, $status);
        }
        header("Location: ../View/php/employeDashboard.php?msg=validation_updated");
        exit();
    }
    elseif (isset($_POST['delete_validation'])) {
        $id = (int)($_POST['validation_id'] ?? 0);
        if ($id > 0) deleteValidation($id);
        header("Location: ../View/php/employeDashboard.php?msg=validation_deleted");
        exit();
    }

    // Passenger Card Status
    elseif (isset($_POST['update_user_status'])) {
        $id     = (int)($_POST['user_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'Active');
        if ($id > 0) {
            updateUserStatus($id, $status);
        }
        header("Location: ../View/php/unblockCard.php?msg=status_updated");
        exit();
    }

    // Verify Payment by Ticket ID
    elseif (isset($_POST['verify_payment'])) {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $details = getTicketDetails($ticket_id);
        if ($details) {
            $status = urlencode($details['Status']);
            header("Location: ../View/php/paymentHandling.php?searched_ticket=$ticket_id&ticket_status=$status");
        } else {
            header("Location: ../View/php/paymentHandling.php?searched_ticket=$ticket_id&error=not_found");
        }
        exit();
    }
}

header("Location: ../View/php/employeDashboard.php");
exit();
?>
