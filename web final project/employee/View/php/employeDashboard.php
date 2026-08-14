<?php
session_start();

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: login.php");
    exit();
}

include "../../Model/EmployeeModel.php";

 
$passengers = getAllPassengers();
$transactions = getAllTransactions();
 
$userID = (int)$_SESSION['UserID'];
$resName = mysqli_query($conn, "SELECT FirstName, LastName FROM users WHERE UserID = $userID");
$empName = "";
if ($row = mysqli_fetch_assoc($resName)) {
    $empName = htmlspecialchars($row['FirstName'] . " " . $row['LastName']);
} else {
    $empName = htmlspecialchars($_SESSION['Mobile']);  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Dhaka Metro</title>

 
    <link rel="stylesheet" href="../css/employDashboard.css">
</head>

<body>
   
    <?php require_once('../../../admin/view/employeeheader.php'); ?>

    <div class="dashboard-container">
        <h2>Employee Dashboard</h2>
        <p class="welcome-text">Welcome, <?php echo $empName; ?> (Staff)</p>

        

        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <div class="dashboard-section" style="flex: 1; text-align: center; padding: 40px 20px;">
                <h3 style="border: none; margin-bottom: 10px;">💳 Passenger Management</h3>
                <p style="color: #555; margin-bottom: 20px;">Block or unblock passenger cards.</p>
                <a href="unblockCard.php" class="btn-add" style="text-decoration: none; display: inline-block;">Go to Passengers</a>
            </div>

            <div class="dashboard-section" style="flex: 1; text-align: center; padding: 40px 20px;">
                <h3 style="border: none; margin-bottom: 10px;">💰 Payments & Transactions</h3>
                <p style="color: #555; margin-bottom: 20px;">Verify payments and view transaction history.</p>
                <a href="paymentHandling.php" class="btn-add" style="text-decoration: none; display: inline-block;">Go to Payments</a>
            </div>
        </div>

    </div>

 
    <?php require_once('footer.php'); ?>
</body>
</html>
