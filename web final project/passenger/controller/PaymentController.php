<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

include "../model/Database.php";
include "../model/PaymentModel.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userID = (int)$_SESSION['UserID'];

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['submit'])) {

    $amount      = trim($_POST['amount'] ?? '');
    $method      = trim($_POST['payment_method'] ?? '');
    $routeID     = (int)($_POST['route_id'] ?? 0);
    $journeyDate = trim($_POST['journey_date'] ?? date('Y-m-d'));

    if ($amount === "" || !is_numeric($amount) || $amount <= 0) {
        $_SESSION['alert'] = "Enter a valid amount.";
        header("Location: ../view/payment.php?route_id=$routeID");
        exit();
    }

    if ($method === "") {
        $_SESSION['alert'] = "Please select a payment method.";
        header("Location: ../view/payment.php?route_id=$routeID");
        exit();
    }

    $walletBalance = getWalletBalance($userID);

    if ($amount > $walletBalance) {
        $_SESSION['alert'] = "Insufficient balance! Your wallet has " . number_format($walletBalance, 2) . " BDT, but ticket fare is " . number_format($amount, 2) . " BDT.";
        header("Location: ../view/payment.php?route_id=$routeID");
        exit();
    }

    $ticketID = createTicket($userID, $routeID, $journeyDate);

    if ($ticketID > 0) {
        deductWalletBalance($userID, $amount);
        addTransaction($userID, $amount, "Debit", "Ticket #$ticketID ($method)");
        addPayment($ticketID, $amount);

        // Clear temporary journey session
        unset($_SESSION['temp_journey']);

        $_SESSION['alert_success'] = "Payment successful! Ticket #$ticketID confirmed. " . number_format($amount, 2) . " BDT deducted.";
        header("Location: ../view/payment_record_page.php?type=tickets&msg=success");
        exit();
    } else {
        $_SESSION['alert'] = "Failed to generate ticket. Please try again.";
        header("Location: ../view/payment.php?route_id=$routeID");
        exit();
    }
} else {
    header("Location: ../view/dashboard_after_login.php");
    exit();
}
?>
