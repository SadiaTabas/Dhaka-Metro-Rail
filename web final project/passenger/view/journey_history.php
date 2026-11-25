<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

if (isset($_SESSION['RoleID']) && (int)$_SESSION['RoleID'] !== 3) {
    if ((int)$_SESSION['RoleID'] === 1) {
        header("Location: ../../admin/view/admindashboard.php");
        exit();
    } elseif ((int)$_SESSION['RoleID'] === 2) {
        header("Location: ../../employee/View/php/employeDashboard.php");
        exit();
    }
}

include "../model/Database.php";
include "../model/JourneyModel.php";
include "header.php";

$userId = (int)$_SESSION['UserID'];

$passengerRes = getPassenger($userId);
$passenger = ($passengerRes && method_exists($passengerRes, 'fetch_assoc')) ? $passengerRes->fetch_assoc() : ($passengerRes ? mysqli_fetch_assoc($passengerRes) : null);
$passengerName = $passenger ? trim($passenger['FirstName'] . " " . $passenger['LastName']) : ($_SESSION['UserName'] ?? "Passenger");

$totalRes = getTotalTicket($userId);
$total = ($totalRes && method_exists($totalRes, 'fetch_assoc')) ? $totalRes->fetch_assoc() : ($totalRes ? mysqli_fetch_assoc($totalRes) : ['total' => 0]);

$journeys = getJourneyHistory($userId);
?>

<link rel="stylesheet" href="journey_history.css">
<link rel="stylesheet" href="dashboard.css">

<main class="main-content">
    <div class="dashboard-content">
        <div class="history-header">
            <h2>Dhaka Metro Rail – Journey History</h2>
            <p>Review and manage all your past journey logs on MRT Line-6</p>
        </div>

        <div class="stats-bar">
            <div class="stat-item">
                <span class="stat-label">Passenger Name:</span>
                <span class="stat-val"><?= htmlspecialchars($passengerName) ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Total Completed Bookings:</span>
                <span class="stat-val stat-highlight"><?= (int)($total['total'] ?? 0) ?></span>
            </div>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <div class="alert-box success-alert">Selected journey history records deleted successfully.</div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert-box error-alert">Please select at least one journey record to delete.</div>
        <?php endif; ?>

        <form method="post" action="../controller/JourneyController.php" onsubmit="return validateDelete();">
            <div class="table-responsive">
                <table class="history-table-grid">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Select</th>
                            <th>#</th>
                            <th>Departure Station</th>
                            <th>Destination Station</th>
                            <th>Date of Journey</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($journeys && $journeys->num_rows > 0):
                            while ($row = $journeys->fetch_assoc()):
                        ?>
                            <tr>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="ticket_id[]" value="<?= $row['TicketID'] ?>" class="row-checkbox">
                                </td>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['FromStation']) ?></strong></td>
                                <td><strong><?= htmlspecialchars($row['ToStation']) ?></strong></td>
                                <td><?= htmlspecialchars($row['JourneyDate']) ?></td>
                                <td><span class="status-badge status-confirmed"><?= htmlspecialchars($row['Status'] ?? 'Confirmed') ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center; padding: 30px; color:#666;">
                                    No journey records found. <a href="dashboard_after_login.php" style="color:#006a4e; font-weight:bold;">Book a new journey</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="history-actions">
                <button type="submit" class="danger-btn">🗑️ Delete Selected</button>
                <a href="payment_record_page.php" class="back-btn">← Back to Portal</a>
            </div>
        </form>
    </div>
</main>

<script src="journey.js"></script>
<?php include "footer.php"; ?>
