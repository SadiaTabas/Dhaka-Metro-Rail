<?php
include "Database.php";

function getWalletBalance($userID) {
    global $conn;
    if (!$conn) return 0;
    $userID = (int)$userID;
    
    $stmt = $conn->prepare("SELECT Balance FROM wallet WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return (float)$row['Balance'];
        }
        $stmt->close();
    }
    
    // If wallet record does not exist for this user, auto-initialize
    $initStmt = $conn->prepare("INSERT IGNORE INTO wallet (UserID, Balance) VALUES (?, 500.00)");
    if ($initStmt) {
        $initStmt->bind_param("i", $userID);
        $initStmt->execute();
        $initStmt->close();
        return 500.00;
    }

    return 0;
}

function deductWalletBalance($userID, $amount) {
    global $conn;
    if (!$conn) return false;
    $userID = (int)$userID;
    $amount = (float)$amount;

    $stmt = $conn->prepare("UPDATE wallet SET Balance = Balance - ? WHERE UserID = ? AND Balance >= ?");
    if ($stmt) {
        $stmt->bind_param("did", $amount, $userID, $amount);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    return false;
}

function addTransaction($userID, $amount, $type, $ref) {
    global $conn;
    if (!$conn) return false;
    $userID = (int)$userID;
    $amount = (float)$amount;

    $stmt = $conn->prepare("INSERT INTO transactions (UserID, Amount, Type, Reference) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("idss", $userID, $amount, $type, $ref);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    return false;
}

function addPayment($ticketID, $amount) {
    global $conn;
    if (!$conn) return false;
    $ticketID = (int)$ticketID;
    $amount = (float)$amount;

    $stmt = $conn->prepare("INSERT INTO payments (TicketID, Amount, Status) VALUES (?, ?, 'Success')");
    if ($stmt) {
        $stmt->bind_param("id", $ticketID, $amount);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    return false;
}

function getRouteFare($routeID) {
    global $conn;
    if (!$conn) return 0;
    $routeID = (int)$routeID;

    $stmt = $conn->prepare("SELECT Fare FROM routes WHERE RouteID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $routeID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return (float)$row['Fare'];
        }
        $stmt->close();
    }
    return 0;
}

function createTicket($userID, $routeID, $journeyDate) {
    global $conn;
    if (!$conn) return 0;
    $userID = (int)$userID;
    $routeID = (int)$routeID;

    $stmt = $conn->prepare("INSERT INTO tickets (UserID, RouteID, JourneyDate, Status) VALUES (?, ?, ?, 'Confirmed')");
    if ($stmt) {
        $stmt->bind_param("iis", $userID, $routeID, $journeyDate);
        $stmt->execute();
        $ticketId = $stmt->insert_id;
        $stmt->close();
        return $ticketId;
    }
    return 0;
}
?>
