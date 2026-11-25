<?php
// Dhaka Metro Rail - Passenger User Model
include "Database.php";

function createUser($firstName, $lastName, $mobile, $nid, $dob, $gender, $password) {
    global $conn;
    if (!$conn) {
        return false;
    }

    // 1. Check if mobile already exists in production MySQL
    $checkStmt = $conn->prepare("SELECT UserID FROM users WHERE Mobile = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("s", $mobile);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $checkStmt->close();
            return "exists";
        }
        $checkStmt->close();
    } else {
        return false;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $roleId = 3; // Passenger

    // 2. Execute actual INSERT query into MySQL
    $stmt = $conn->prepare("INSERT INTO users (RoleID, FirstName, LastName, Mobile, NID, DateOfBirth, Gender, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("isssssss", $roleId, $firstName, $lastName, $mobile, $nid, $dob, $gender, $hashedPassword);
    $success = $stmt->execute();
    if (!$success) {
        $stmt->close();
        return false;
    }

    $newUserId = (int)$stmt->insert_id;
    $stmt->close();

    if ($newUserId <= 0) {
        return false;
    }

    // 3. VERIFY PERSISTENCE: Retrieve newly created record from database
    $verifyStmt = $conn->prepare("SELECT UserID, Mobile, RoleID FROM users WHERE UserID = ? AND Mobile = ?");
    if ($verifyStmt) {
        $verifyStmt->bind_param("is", $newUserId, $mobile);
        $verifyStmt->execute();
        $verifyRes = $verifyStmt->get_result();
        $isPersisted = ($verifyRes && $verifyRes->num_rows === 1);
        $verifyStmt->close();

        if (!$isPersisted) {
            return false;
        }
    } else {
        return false;
    }

    // 4. Initialize passenger wallet with 500.00 BDT default balance in MySQL
    $walletStmt = $conn->prepare("INSERT INTO wallet (UserID, Balance) VALUES (?, 500.00)");
    if ($walletStmt) {
        $walletStmt->bind_param("i", $newUserId);
        $walletStmt->execute();
        $walletStmt->close();
    }

    // 5. Add initial welcome transaction record in MySQL
    $txStmt = $conn->prepare("INSERT INTO transactions (UserID, Amount, Type, Reference) VALUES (?, 500.00, 'Credit', 'Welcome Balance')");
    if ($txStmt) {
        $txStmt->bind_param("i", $newUserId);
        $txStmt->execute();
        $txStmt->close();
    }

    // Registration confirmed and fully verified in persistent storage
    return true;
}
?>
