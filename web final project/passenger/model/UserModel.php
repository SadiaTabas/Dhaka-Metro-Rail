<?php
include "Database.php";

function createUser($firstName, $lastName, $mobile, $nid, $dob, $gender, $password) {
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
            return "exists";
        }
        $checkStmt->close();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $roleId = 3; // Passenger

    $stmt = $conn->prepare("INSERT INTO users (RoleID, FirstName, LastName, Mobile, NID, DateOfBirth, Gender, Password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) return false;

    $stmt->bind_param("isssssss", $roleId, $firstName, $lastName, $mobile, $nid, $dob, $gender, $hashedPassword);
    $success = $stmt->execute();
    if ($success) {
        $newUserId = $stmt->insert_id;
        $stmt->close();

        // Initialize passenger wallet with 500 BDT default balance for immediate ticket booking
        $walletStmt = $conn->prepare("INSERT INTO wallet (UserID, Balance) VALUES (?, 500.00)");
        if ($walletStmt) {
            $walletStmt->bind_param("i", $newUserId);
            $walletStmt->execute();
            $walletStmt->close();
        }

        // Add initial credit transaction
        $txStmt = $conn->prepare("INSERT INTO transactions (UserID, Amount, Type, Reference) VALUES (?, 500.00, 'Credit', 'Welcome Balance')");
        if ($txStmt) {
            $txStmt->bind_param("i", $newUserId);
            $txStmt->execute();
            $txStmt->close();
        }

        return true;
    }
    $stmt->close();
    return false;
}
?>
