<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: login.php");
    exit();
}

include "../../Model/EmployeeModel.php";

$transactions = null;
if (isset($_GET['searched_ticket'])) {
    $transactions = getTransactionsByTicketID($_GET['searched_ticket']);
} else {
    $transactions = getAllTransactions();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Handling - Dhaka Metro</title>
    <link rel="stylesheet" href="../css/employDashboard.css">
    <style>
        .input-verify {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc !important;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #fff;
            color: #333;
        }
        .input-verify:focus {
            border-color: #28a745 !important;
            outline: none;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
        }
    </style>
</head>

<body>
    
    <?php require_once('../../../admin/view/employeeheader.php'); ?>

    <div class="dashboard-container">
        <h2>Payment & Transactions</h2>

      
        <section class="dashboard-section">
            <h3>Verify Payment Status</h3>
            <div class="add-form" style="display: flex; flex-direction: column; gap: 10px;">
                <form action="../../Controller/EmployeeController.php" method="POST" style="width: 100%; display: flex; gap: 15px;">
                    <input type="text" name="ticket_id" placeholder="Enter Ticket ID" required class="input-verify" value="<?php echo isset($_GET['searched_ticket']) ? htmlspecialchars($_GET['searched_ticket']) : ''; ?>">
                    <button type="submit" name="verify_payment" class="btn-add">Verify</button>
                </form>
            </div>
            
            <?php if (isset($_GET['ticket_status'])): ?>
                <div style="margin-top: 15px; padding: 15px; background: #e8f5e9; border: 1px solid #28a745; border-radius: 4px; color: #1e7e34; font-size: 16px;">
                    <strong>Ticket Status:</strong> <?php echo htmlspecialchars($_GET['ticket_status']); ?>
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] == 'not_found'): ?>
                <div style="margin-top: 15px; padding: 15px; background: #fdf2f2; border: 1px solid #ff4c4c; border-radius: 4px; color: #ff4c4c; font-size: 16px;">
                    <strong>Ticket not found.</strong>
                </div>
            <?php endif; ?>
        </section>

   
        <section class="dashboard-section">
            <h3>Transaction History</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($transactions && mysqli_num_rows($transactions) > 0) {
                            while($row = mysqli_fetch_assoc($transactions)) { ?>
                            <tr>
                                <td><?php echo $row['TransactionID']; ?></td>
                                <td><?php echo $row['FirstName'] . " " . $row['LastName']; ?></td>
                                <td><?php echo number_format($row['Amount'], 2); ?> ৳</td>
                                <td><?php echo $row['Type']; ?></td>
                                <td><?php echo $row['Reference']; ?></td>
                                <td><?php echo $row['CreatedAt']; ?></td>
                            </tr>
                            <?php } 
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center;'>No transactions found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

 
    <?php require_once('footer.php'); ?>
</body>
</html>
