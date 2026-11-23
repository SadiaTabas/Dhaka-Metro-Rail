<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
if (!isset($_SESSION['UserID']) || $_SESSION['RoleID'] != 2) {
    header("Location: login.php");
    exit();
}

include "../../Model/EmployeeModel.php";

$searchedTicket = isset($_GET['searched_ticket']) ? (int)$_GET['searched_ticket'] : 0;
$transactions = null;

if ($searchedTicket > 0) {
    $transactions = getTransactionsByTicketID($searchedTicket);
} else {
    $transactions = getAllTransactions();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Handling & Verification - Dhaka Metro</title>
    <link rel="stylesheet" href="../css/employDashboard.css">
    <link rel="stylesheet" href="../../../passenger/view/dashboard.css">
</head>
<body>

<?php require_once('../../../admin/view/employeeheader.php'); ?>

<main class="main-content">
    <div class="user-welcome-bar">
        <div class="welcome-left">
            <h2>Payment Verification & Audit Log</h2>
            <p>Verify ticket validity and monitor digital transit transactions</p>
        </div>
        <div class="welcome-right">
            <a href="employeDashboard.php" class="back-btn" style="text-decoration:none; padding:10px 16px; background:#f1f5f9; border-radius:6px; font-weight:600;">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Verification Section -->
    <section class="section-box" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:25px; margin-bottom:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <h3 style="color:#006a4e; margin-bottom:12px; font-size:18px;">🔍 Verify Ticket Booking Status</h3>
        <p style="color:#718096; font-size:14px; margin-bottom:18px;">Enter a Ticket ID to look up its confirmation and transaction records.</p>

        <form action="../../Controller/EmployeeController.php" method="POST" style="display:flex; gap:12px; max-width:600px; flex-wrap:wrap;">
            <input type="number" name="ticket_id" placeholder="Enter Ticket ID (e.g. 1)" required style="flex:1; min-width:200px; padding:12px; border:1.5px solid #cbd5e1; border-radius:6px;" value="<?php echo $searchedTicket > 0 ? $searchedTicket : ''; ?>">
            <button type="submit" name="verify_payment" class="btn-top-action" style="border:none; cursor:pointer;">Verify Status</button>
            <?php if ($searchedTicket > 0): ?>
                <a href="paymentHandling.php" style="display:flex; align-items:center; padding:10px 16px; color:#64748b; text-decoration:none;">Clear Filter</a>
            <?php endif; ?>
        </form>

        <?php if (isset($_GET['ticket_status'])): ?>
            <div style="margin-top:20px; padding:15px; background:#e8f5e9; border-left:4px solid #28a745; border-radius:6px; color:#1e7e34;">
                <strong>Ticket #<?= $searchedTicket ?>:</strong> Current Status is <strong><?= htmlspecialchars($_GET['ticket_status']) ?></strong>
            </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] === 'not_found'): ?>
            <div style="margin-top:20px; padding:15px; background:#fee2e2; border-left:4px solid #ef4444; border-radius:6px; color:#b91c1c;">
                <strong>Notice:</strong> No ticket found matching Ticket ID #<?= $searchedTicket ?>.
            </div>
        <?php endif; ?>
    </section>

    <!-- Transactions Table -->
    <section class="section-box" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="color:#006a4e; font-size:18px;">
                <?php echo ($searchedTicket > 0) ? "Transactions for Ticket #$searchedTicket" : "Recent Station Transactions"; ?>
            </h3>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0;">
                        <th style="padding:12px; text-align:left;">TX ID</th>
                        <th style="padding:12px; text-align:left;">Passenger</th>
                        <th style="padding:12px; text-align:left;">Amount</th>
                        <th style="padding:12px; text-align:left;">Type</th>
                        <th style="padding:12px; text-align:left;">Reference</th>
                        <th style="padding:12px; text-align:left;">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($transactions && mysqli_num_rows($transactions) > 0) {
                        while($row = mysqli_fetch_assoc($transactions)) { 
                            $isCredit = ($row['Type'] === 'Credit');
                        ?>
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <td style="padding:12px;"><strong>#<?php echo $row['TransactionID']; ?></strong></td>
                            <td style="padding:12px;"><?php echo htmlspecialchars($row['FirstName'] . " " . $row['LastName']); ?></td>
                            <td style="padding:12px; font-weight:700; color:<?php echo $isCredit ? '#15803d' : '#006a4e'; ?>;">
                                <?php echo ($isCredit ? '+' : '-') . number_format($row['Amount'], 2); ?> ৳
                            </td>
                            <td style="padding:12px;">
                                <span style="display:inline-block; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:700; background:<?php echo $isCredit ? '#dcfce7' : '#e0f2fe'; ?>; color:<?php echo $isCredit ? '#15803d' : '#0369a1'; ?>;">
                                    <?php echo htmlspecialchars($row['Type']); ?>
                                </span>
                            </td>
                            <td style="padding:12px; color:#64748b;"><?php echo htmlspecialchars($row['Reference']); ?></td>
                            <td style="padding:12px; color:#64748b; font-size:13px;"><?php echo htmlspecialchars($row['CreatedAt']); ?></td>
                        </tr>
                        <?php } 
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding:35px; color:#94a3b8;'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require_once('footer.php'); ?>
</body>
</html>
