<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/Database.php";
include "../model/RecordModel.php";
include "header.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userID = (int)$_SESSION['UserID'];

$userRes = getUserName($userID);
$user = ($userRes && method_exists($userRes, 'fetch_assoc')) ? $userRes->fetch_assoc() : ($userRes ? mysqli_fetch_assoc($userRes) : null);
$userName = $user ? trim($user['FirstName'] . " " . $user['LastName']) : ($_SESSION['UserName'] ?? "Passenger");

$type = $_GET['type'] ?? "tickets";
$msg = $_GET['msg'] ?? "";
?>
<link rel="stylesheet" href="payment_record_page.css">
<link rel="stylesheet" href="dashboard.css">

<main class="main-content">
    <div class="records-container">
        <div class="user-info-banner">
            <div>
                <h2>Passenger Account Portal</h2>
                <p>Welcome back, <strong><?= htmlspecialchars($userName) ?></strong> (Passenger ID #<?= $userID ?>)</p>
            </div>
            <div>
                <a href="dashboard_after_login.php" class="btn-top-action">Book New Journey</a>
            </div>
        </div>

        <?php if ($msg === 'success'): ?>
            <div class="alert-box success-alert">Transaction completed successfully! Your ticket has been confirmed.</div>
        <?php elseif ($msg === 'deleted'): ?>
            <div class="alert-box success-alert">Payment record successfully removed.</div>
        <?php endif; ?>

        <div class="account-nav-bar">
            <a href="?type=tickets" class="tab-btn <?= ($type === 'tickets') ? 'active-tab' : '' ?>">🎫 My Tickets</a>
            <a href="?type=payments" class="tab-btn <?= ($type === 'payments') ? 'active-tab' : '' ?>">💳 Payment Records</a>
            <a href="journey_history.php" class="tab-btn">🕒 Journey History</a>
            <a href="../../admin/view/Profile_Information.php" class="tab-btn">👤 Edit Profile</a>
        </div>

        <div class="record-content-box">
            <?php if ($type === "tickets"): ?>
                <div class="section-header">
                    <h3>Confirmed Tickets & Transit Passes</h3>
                    <p>List of all ticket reservations under your account</p>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Departure</th>
                                <th>Destination</th>
                                <th>Journey Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tickets = getTicketHistory($userID);
                            if ($tickets && $tickets->num_rows > 0):
                                while ($row = $tickets->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($row['TicketID']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['FromStation']) ?></td>
                                    <td><?= htmlspecialchars($row['ToStation']) ?></td>
                                    <td><?= htmlspecialchars($row['JourneyDate']) ?></td>
                                    <td><span class="status-badge status-confirmed"><?= htmlspecialchars($row['Status']) ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 30px; color:#666;">
                                        No tickets found. <a href="dashboard_after_login.php" style="color:#006a4e; font-weight:bold;">Book your first journey now!</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($type === "payments"): ?>
                <div class="section-header">
                    <h3>Payment & Transaction History</h3>
                    <p>Receipts for ticket bookings and wallet transactions</p>
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $payments = getPaymentHistory($userID);
                            if ($payments && $payments->num_rows > 0):
                                while ($row = $payments->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($row['PaymentID']) ?></strong></td>
                                    <td class="amount-cell"><?= number_format($row['Amount'], 2) ?> ৳</td>
                                    <td><?= htmlspecialchars($row['PaymentDate']) ?></td>
                                    <td><span class="status-badge status-success"><?= htmlspecialchars($row['Status']) ?></span></td>
                                    <td>
                                        <a href="../controller/RecordController.php?delete=<?= $row['PaymentID'] ?>"
                                           class="btn-delete-record"
                                           onclick="return confirm('Are you sure you want to remove this payment record?');">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 30px; color:#666;">
                                        No payment records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include "footer.php"; ?>
