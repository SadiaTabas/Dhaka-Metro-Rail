<?php
include "Database.php";

function getPassenger($userId) {
    global $conn;
    if (!$conn) return false;
    $userId = (int)$userId;
    $stmt = $conn->prepare("SELECT FirstName, LastName FROM users WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function getTotalTicket($userId) {
    global $conn;
    if (!$conn) return false;
    $userId = (int)$userId;
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tickets WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function getJourneyHistory($userId) {
    global $conn;
    if (!$conn) return false;
    $userId = (int)$userId;
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
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function deleteJourney($ticketID, $userID) {
    global $conn;
    if (!$conn) return false;
    $ticketID = (int)$ticketID;
    $userID = (int)$userID;

    if (method_exists($conn, 'begin_transaction')) {
        $conn->begin_transaction();
    } elseif (function_exists('mysqli_begin_transaction')) {
        mysqli_begin_transaction($conn);
    }
    try {
        $pStmt = $conn->prepare("DELETE FROM payments WHERE TicketID = ?");
        if ($pStmt) {
            $pStmt->bind_param("i", $ticketID);
            $pStmt->execute();
            $pStmt->close();
        }

        $tStmt = $conn->prepare("DELETE FROM tickets WHERE TicketID = ? AND UserID = ?");
        if ($tStmt) {
            $tStmt->bind_param("ii", $ticketID, $userID);
            $tStmt->execute();
            $tStmt->close();
        }

        if (method_exists($conn, 'commit')) {
            $conn->commit();
        } elseif (function_exists('mysqli_commit')) {
            mysqli_commit($conn);
        }
        return true;
    } catch (Exception $e) {
        if (method_exists($conn, 'rollback')) {
            $conn->rollback();
        } elseif (function_exists('mysqli_rollback')) {
            mysqli_rollback($conn);
        }
        return false;
    }
}
?>
