<?php
session_start();
include "../Model/EmployeeModel.php";
 
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: ../../admin/view/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    if (isset($_POST['add_station'])) {
        $name = trim($_POST['station_name']);
        if (!empty($name)) addStation($name);
    } 
    elseif (isset($_POST['update_station'])) {
        $id = $_POST['station_id'];
        $name = trim($_POST['station_name']);
        if (!empty($name) && !empty($id)) updateStation($id, $name);
    }
    elseif (isset($_POST['delete_station'])) {
        $id = $_POST['station_id'];
        if (!empty($id)) deleteStation($id);
    }
 
    elseif (isset($_POST['add_schedule'])) {
        $trainName = trim($_POST['train_name']);
        $routeId = $_POST['route_id'];
        $depTime = $_POST['dep_time'];
        $arrTime = $_POST['arr_time'];
        if (!empty($trainName) && !empty($routeId) && !empty($depTime) && !empty($arrTime)) {
            addSchedule($trainName, $routeId, $depTime, $arrTime);
        }
    }
    elseif (isset($_POST['update_schedule'])) {
        $id = $_POST['schedule_id'];
        $trainName = trim($_POST['train_name']);
        $routeId = $_POST['route_id'];
        $depTime = $_POST['dep_time'];
        $arrTime = $_POST['arr_time'];
        if (!empty($id) && !empty($trainName) && !empty($routeId) && !empty($depTime) && !empty($arrTime)) {
            updateSchedule($id, $trainName, $routeId, $depTime, $arrTime);
        }
    }
    elseif (isset($_POST['delete_schedule'])) {
        $id = $_POST['schedule_id'];
        if (!empty($id)) deleteSchedule($id);
    }
 
    elseif (isset($_POST['add_validation'])) {
        $ticketId = $_POST['ticket_id'];
        $status = $_POST['status'];
        $validatedBy = $_SESSION['UserID'];
        if (!empty($ticketId) && !empty($status)) {
            addValidation($ticketId, $status, $validatedBy);
        }
    }
    elseif (isset($_POST['update_validation'])) {
        $id = $_POST['validation_id'];
        $status = $_POST['status'];
        if (!empty($id) && !empty($status)) {
            updateValidationStatus($id, $status);
        }
    }
    elseif (isset($_POST['delete_validation'])) {
        $id = $_POST['validation_id'];
        if (!empty($id)) deleteValidation($id);
    }
 
    elseif (isset($_POST['update_user_status'])) {
        $id = $_POST['user_id'];
        $status = $_POST['status'];
        updateUserStatus($id, $status);
        header("Location: ../View/php/employeDashboard.php?msg=status_updated");
        exit();
    }
 
    elseif (isset($_POST['verify_payment'])) {
        $ticket_id = (int)$_POST['ticket_id'];
        $res = mysqli_query($conn, "SELECT Status FROM tickets WHERE TicketID = $ticket_id");
        if ($row = mysqli_fetch_assoc($res)) {
            $status = urlencode($row['Status']);
            header("Location: ../View/php/paymentHandling.php?searched_ticket=$ticket_id&ticket_status=$status");
        } else {
            header("Location: ../View/php/paymentHandling.php?error=not_found");
        }
        exit();
    }

    
    header("Location: ../View/php/employeDashboard.php");
    exit();
}
?>
