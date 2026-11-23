<?php
include "db.php";

function getAllStations() {
    global $conn;
    if (!$conn) return false;
    return $conn->query("SELECT * FROM stations ORDER BY StationID ASC");
}

function addStation($name) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("INSERT INTO stations (StationName) VALUES (?)");
    if ($stmt) {
        $stmt->bind_param("s", $name);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function updateStation($id, $name) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("UPDATE stations SET StationName = ? WHERE StationID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("si", $name, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function deleteStation($id) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("DELETE FROM stations WHERE StationID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function getAllSchedules() {
    global $conn;
    if (!$conn) return false;
    $sql = "SELECT s.ScheduleID, s.TrainName, s.DepartureTime, s.ArrivalTime, r.RouteID, r.FromStation, r.ToStation 
            FROM schedules s
            LEFT JOIN routes r ON s.RouteID = r.RouteID
            ORDER BY s.ScheduleID ASC";
    return $conn->query($sql);
}

function getAllRoutes() {
    global $conn;
    if (!$conn) return false;
    return $conn->query("SELECT * FROM routes ORDER BY RouteID ASC");
}

function addSchedule($trainName, $routeId, $depTime, $arrTime) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("INSERT INTO schedules (TrainName, RouteID, DepartureTime, ArrivalTime) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $routeId = (int)$routeId;
        $stmt->bind_param("siss", $trainName, $routeId, $depTime, $arrTime);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function updateSchedule($id, $trainName, $routeId, $depTime, $arrTime) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("UPDATE schedules SET TrainName = ?, RouteID = ?, DepartureTime = ?, ArrivalTime = ? WHERE ScheduleID = ?");
    if ($stmt) {
        $id = (int)$id;
        $routeId = (int)$routeId;
        $stmt->bind_param("sissi", $trainName, $routeId, $depTime, $arrTime, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function deleteSchedule($id) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("DELETE FROM schedules WHERE ScheduleID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function getAllValidations() {
    global $conn;
    if (!$conn) return false;
    $sql = "SELECT v.ValidationID, v.TicketID, v.Status, v.ValidationTime, u.FirstName, u.LastName 
            FROM ticket_validations v
            LEFT JOIN users u ON v.ValidatedBy = u.UserID
            ORDER BY v.ValidationTime DESC";
    return $conn->query($sql);
}

function addValidation($ticketId, $status, $validatedBy) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("INSERT INTO ticket_validations (TicketID, Status, ValidatedBy) VALUES (?, ?, ?)");
    if ($stmt) {
        $ticketId = (int)$ticketId;
        $validatedBy = (int)$validatedBy;
        $stmt->bind_param("isi", $ticketId, $status, $validatedBy);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function updateValidationStatus($id, $status) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("UPDATE ticket_validations SET Status = ? WHERE ValidationID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("si", $status, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function deleteValidation($id) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("DELETE FROM ticket_validations WHERE ValidationID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("i", $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function getAllPassengers() {
    global $conn;
    if (!$conn) return false;
    return $conn->query("SELECT UserID, FirstName, LastName, Mobile, Status FROM users WHERE RoleID = 3 ORDER BY UserID ASC");
}

function updateUserStatus($id, $status) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("UPDATE users SET Status = ? WHERE UserID = ?");
    if ($stmt) {
        $id = (int)$id;
        $stmt->bind_param("si", $status, $id);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function getAllTransactions() {
    global $conn;
    if (!$conn) return false;
    $sql = "SELECT t.TransactionID, t.Amount, t.Type, t.Reference, t.CreatedAt, u.FirstName, u.LastName 
            FROM transactions t 
            LEFT JOIN users u ON t.UserID = u.UserID 
            ORDER BY t.TransactionID DESC";
    return $conn->query($sql);
}

function getTransactionsByTicketID($ticket_id) {
    global $conn;
    if (!$conn) return false;
    $ticket_id = (int)$ticket_id;
    $stmt = $conn->prepare("SELECT t.TransactionID, t.Amount, t.Type, t.Reference, t.CreatedAt, u.FirstName, u.LastName 
            FROM transactions t 
            JOIN tickets tk ON t.UserID = tk.UserID
            LEFT JOIN users u ON t.UserID = u.UserID 
            WHERE tk.TicketID = ?
            ORDER BY t.TransactionID DESC");
    if ($stmt) {
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function getTicketDetails($ticket_id) {
    global $conn;
    if (!$conn) return false;
    $ticket_id = (int)$ticket_id;
    $stmt = $conn->prepare("SELECT Status FROM tickets WHERE TicketID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }
    return false;
}
?>
