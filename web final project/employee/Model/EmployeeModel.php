<?php
include "db.php";

 
function getAllStations() {
    global $conn;
    return mysqli_query($conn, "SELECT * FROM stations ORDER BY StationID ASC");
}

function addStation($name) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $name);
    $sql = "INSERT INTO stations (StationName) VALUES ('$name')";
    return mysqli_query($conn, $sql);
}

function updateStation($id, $name) {
    global $conn;
    $id = (int)$id;
    $name = mysqli_real_escape_string($conn, $name);
    $sql = "UPDATE stations SET StationName = '$name' WHERE StationID = $id";
    return mysqli_query($conn, $sql);
}

function deleteStation($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM stations WHERE StationID = $id";
    return mysqli_query($conn, $sql);
}

 
function getAllSchedules() {
    global $conn;
 
    $sql = "SELECT s.ScheduleID, s.TrainName, s.DepartureTime, s.ArrivalTime, r.RouteID, r.FromStation, r.ToStation 
            FROM schedules s
            LEFT JOIN routes r ON s.RouteID = r.RouteID
            ORDER BY s.ScheduleID ASC";
    return mysqli_query($conn, $sql);
}

function getAllRoutes() {
    global $conn;
    return mysqli_query($conn, "SELECT * FROM routes ORDER BY RouteID ASC");
}

function addSchedule($trainName, $routeId, $depTime, $arrTime) {
    global $conn;
    $trainName = mysqli_real_escape_string($conn, $trainName);
    $routeId = (int)$routeId;
    $depTime = mysqli_real_escape_string($conn, $depTime);
    $arrTime = mysqli_real_escape_string($conn, $arrTime);
    $sql = "INSERT INTO schedules (TrainName, RouteID, DepartureTime, ArrivalTime) 
            VALUES ('$trainName', $routeId, '$depTime', '$arrTime')";
    return mysqli_query($conn, $sql);
}

function updateSchedule($id, $trainName, $routeId, $depTime, $arrTime) {
    global $conn;
    $id = (int)$id;
    $trainName = mysqli_real_escape_string($conn, $trainName);
    $routeId = (int)$routeId;
    $depTime = mysqli_real_escape_string($conn, $depTime);
    $arrTime = mysqli_real_escape_string($conn, $arrTime);
    $sql = "UPDATE schedules 
            SET TrainName = '$trainName', RouteID = $routeId, DepartureTime = '$depTime', ArrivalTime = '$arrTime'
            WHERE ScheduleID = $id";
    return mysqli_query($conn, $sql);
}

function deleteSchedule($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM schedules WHERE ScheduleID = $id";
    return mysqli_query($conn, $sql);
}

 
function getAllValidations() {
    global $conn;
    $sql = "SELECT v.ValidationID, v.TicketID, v.Status, v.ValidationTime, u.FirstName, u.LastName 
            FROM ticket_validations v
            LEFT JOIN users u ON v.ValidatedBy = u.UserID
            ORDER BY v.ValidationTime DESC";
    return mysqli_query($conn, $sql);
}

function addValidation($ticketId, $status, $validatedBy) {
    global $conn;
    $ticketId = (int)$ticketId;
    $validatedBy = (int)$validatedBy;
    $status = mysqli_real_escape_string($conn, $status);
    $sql = "INSERT INTO ticket_validations (TicketID, Status, ValidatedBy) 
            VALUES ($ticketId, '$status', $validatedBy)";
    return mysqli_query($conn, $sql);
}

function updateValidationStatus($id, $status) {
    global $conn;
    $id = (int)$id;
    $status = mysqli_real_escape_string($conn, $status);
    $sql = "UPDATE ticket_validations SET Status = '$status' WHERE ValidationID = $id";
    return mysqli_query($conn, $sql);
}

function deleteValidation($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM ticket_validations WHERE ValidationID = $id";
    return mysqli_query($conn, $sql);
}
 
function getAllPassengers() {
    global $conn;
    return mysqli_query($conn, "SELECT UserID, FirstName, LastName, Mobile, Status FROM users WHERE RoleID = 3 ORDER BY UserID ASC");
}

function updateUserStatus($id, $status) {
    global $conn;
    $id = (int)$id;
    $status = mysqli_real_escape_string($conn, $status);
    $sql = "UPDATE users SET Status = '$status' WHERE UserID = $id";
    return mysqli_query($conn, $sql);
}

 
function getAllTransactions() {
    global $conn;
    $sql = "SELECT t.TransactionID, t.Amount, t.Type, t.Reference, t.CreatedAt, u.FirstName, u.LastName 
            FROM transactions t 
            LEFT JOIN users u ON t.UserID = u.UserID 
            ORDER BY t.TransactionID DESC";
    return mysqli_query($conn, $sql);
}

function getTransactionsByTicketID($ticket_id) {
    global $conn;
    $ticket_id = (int)$ticket_id;
    $sql = "SELECT t.TransactionID, t.Amount, t.Type, t.Reference, t.CreatedAt, u.FirstName, u.LastName 
            FROM transactions t 
            JOIN tickets tk ON t.UserID = tk.UserID
            LEFT JOIN users u ON t.UserID = u.UserID 
            WHERE tk.TicketID = $ticket_id
            ORDER BY t.TransactionID DESC";
    return mysqli_query($conn, $sql);
}

function getTicketDetails($ticket_id) {
    global $conn;
    $ticket_id = (int)$ticket_id;
    $res = mysqli_query($conn, "SELECT Status FROM tickets WHERE TicketID = $ticket_id");
    if($row = mysqli_fetch_assoc($res)) {
        return $row;
    }
    return false;
}
?>
