<?php
require_once __DIR__ . '/Database.php';

function getPasswordByMobile($mobile) {
    global $conn;
    if (!$conn) return null;

    $stmt = $conn->prepare("SELECT Password FROM users WHERE Mobile = ?");
    if ($stmt) {
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }
    return null;
}

function updatePasswordByMobile($mobile, $newPassword) {
    global $conn;
    if (!$conn) return false;

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET Password = ? WHERE Mobile = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $hashedPassword, $mobile);
        $res = $stmt->execute();
        $stmt->close();
        return $res;
    }
    return false;
}
?>
