<?php
include "Database.php";

function getUserProfile($userId) {
    global $conn;
    if (!$conn) return false;
    $userId = (int)$userId;

    $stmt = $conn->prepare("SELECT UserID, FirstName, LastName, NID, DateOfBirth, Gender, Mobile FROM users WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function updateUserProfile($userId, $firstName, $lastName, $dob, $gender) {
    global $conn;
    if (!$conn) return false;
    $userId = (int)$userId;

    $stmt = $conn->prepare("UPDATE users SET FirstName = ?, LastName = ?, DateOfBirth = ?, Gender = ? WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("ssssi", $firstName, $lastName, $dob, $gender, $userId);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}
?>
