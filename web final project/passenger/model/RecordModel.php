<?php
include "Database.php";

function getUserName($userID) {
    global $conn;
    if (!$conn) return false;
    $userID = (int)$userID;

    $stmt = $conn->prepare("SELECT FirstName, LastName FROM users WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function getTicketHistory($userID) {
    global $conn;
    if (!$conn) return false;
    $userID = (int)$userID;

    $stmt = $conn->prepare("SELECT 
                t.TicketID,
                r.FromStation,
                r.ToStation,
                t.JourneyDate,
                t.Status
            FROM tickets t
            JOIN routes r ON t.RouteID = r.RouteID
            WHERE t.UserID = ?
            ORDER BY t.TicketID DESC");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function getPaymentHistory($userID) {
    global $conn;
    if (!$conn) return false;
    $userID = (int)$userID;

    $stmt = $conn->prepare("SELECT 
                p.PaymentID,
                p.Amount,
                p.PaymentDate,
                p.Status
            FROM payments p
            JOIN tickets t ON p.TicketID = t.TicketID
            WHERE t.UserID = ?
            ORDER BY p.PaymentDate DESC");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function deletePayment($paymentID) {
    global $conn;
    if (!$conn) return false;
    $paymentID = (int)$paymentID;

    $stmt = $conn->prepare("DELETE FROM payments WHERE PaymentID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $paymentID);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}
?>
