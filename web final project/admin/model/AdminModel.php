<?php
include "Database.php";

function getTotalStaff() {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE RoleID = 2");
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}

function getTotalRoutes() {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM routes");
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}

function getTotalPassengers() {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE RoleID = 3");
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}

function getTotalBookings() {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COUNT(*) AS total FROM tickets");
    return ($res && $row = $res->fetch_assoc()) ? (int)$row['total'] : 0;
}

function getTotalRevenue() {
    global $conn;
    if (!$conn) return 0;
    $res = $conn->query("SELECT COALESCE(SUM(Amount), 0) AS total FROM payments WHERE Status = 'Success'");
    return ($res && $row = $res->fetch_assoc()) ? (float)$row['total'] : 0;
}

if (!function_exists('getAllRoutes')) {
    function getAllRoutes() {
        global $conn;
        if (!$conn) return false;
        return $conn->query("SELECT * FROM routes ORDER BY RouteID ASC");
    }
}

function deleteRoute($routeId) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("DELETE FROM routes WHERE RouteID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $routeId);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function addRoute($from, $to, $fare) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("INSERT INTO routes (FromStation, ToStation, Fare) VALUES (?, ?, ?)");
    if ($stmt) {
        $fare = (float)$fare;
        $stmt->bind_param("ssd", $from, $to, $fare);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function getAllStaff() {
    global $conn;
    if (!$conn) return false;
    return $conn->query("SELECT * FROM users WHERE RoleID = 2 ORDER BY UserID ASC");
}

function deleteStaff($mobile) {
    global $conn;
    if (!$conn) return false;
    $stmt = $conn->prepare("DELETE FROM users WHERE Mobile = ? AND RoleID = 2");
    if ($stmt) {
        $stmt->bind_param("s", $mobile);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}

function addStaff($firstName, $lastName, $mobile, $password, $roleId = 2) {
    global $conn;
    if (!$conn) return false;

    // Check if mobile already exists
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Mobile = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("s", $mobile);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            return false;
        }
        $checkStmt->close();
    }

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (RoleID, FirstName, LastName, Mobile, Password) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $roleId, $firstName, $lastName, $mobile, $hashedPass);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}
?>
