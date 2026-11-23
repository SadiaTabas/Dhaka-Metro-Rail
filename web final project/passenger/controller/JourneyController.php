<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/JourneyModel.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userId = (int)$_SESSION['UserID'];

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (!isset($_POST['ticket_id']) || empty($_POST['ticket_id'])) {
        header("Location: ../view/journey_history.php?error=no_selection");
        exit();
    }

    $ticketIds = (array)$_POST['ticket_id'];

    foreach ($ticketIds as $id) {
        deleteJourney((int)$id, $userId);
    }

    header("Location: ../view/journey_history.php?msg=deleted");
    exit();
} else {
    header("Location: ../view/journey_history.php");
    exit();
}
?>
