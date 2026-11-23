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

$userID = (int)$_SESSION['UserID'];
$empName = $_SESSION['UserName'] ?? "Staff";

if ($conn) {
    $resName = $conn->query("SELECT FirstName, LastName FROM users WHERE UserID = $userID");
    if ($resName && $row = $resName->fetch_assoc()) {
        $empName = trim($row['FirstName'] . " " . $row['LastName']);
        $_SESSION['UserName'] = $empName;
    }
}

// Live statistics
$totalPassengers = 0;
$activeCards = 0;
$blockedCards = 0;
$totalTransactions = 0;

if ($conn) {
    $pRes = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE RoleID = 3");
    if ($pRes && $row = $pRes->fetch_assoc()) $totalPassengers = (int)$row['cnt'];

    $actRes = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE RoleID = 3 AND (Status = 'Active' OR Status IS NULL)");
    if ($actRes && $row = $actRes->fetch_assoc()) $activeCards = (int)$row['cnt'];

    $blkRes = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE RoleID = 3 AND Status = 'Blocked'");
    if ($blkRes && $row = $blkRes->fetch_assoc()) $blockedCards = (int)$row['cnt'];

    $txRes = $conn->query("SELECT COUNT(*) as cnt FROM transactions");
    if ($txRes && $row = $txRes->fetch_assoc()) $totalTransactions = (int)$row['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Operations - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="../css/employDashboard.css">
    <link rel="stylesheet" href="../../../passenger/view/dashboard.css">
</head>
<body>

<?php require_once('../../../admin/view/employeeheader.php'); ?>

<main class="main-content">
    <div class="user-welcome-bar">
        <div class="welcome-left">
            <h2>Employee Operations Portal</h2>
            <p>Welcome back, <strong><?= htmlspecialchars($empName) ?></strong> (Staff ID #<?= $userID ?>)</p>
        </div>
        <div class="welcome-right">
            <span class="status-badge status-confirmed">System Operational</span>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'status_updated'): ?>
            <div class="alert-box success-alert">Passenger account status updated successfully.</div>
        <?php elseif ($_GET['msg'] === 'password_updated'): ?>
            <div class="alert-box success-alert">Your password has been updated successfully.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="info-cards-container">
        <div class="info-card">
            <div class="info-icon">👥</div>
            <h3>Total Passengers</h3>
            <p class="stat-highlight" style="font-size:24px; font-weight:800; color:#006a4e; margin:6px 0;"><?= $totalPassengers ?></p>
            <small>Registered Accounts</small>
        </div>
        <div class="info-card">
            <div class="info-icon">💳</div>
            <h3>Active Transit Cards</h3>
            <p class="stat-highlight" style="font-size:24px; font-weight:800; color:#15803d; margin:6px 0;"><?= $activeCards ?></p>
            <small>Cleared for travel</small>
        </div>
        <div class="info-card">
            <div class="info-icon">🚫</div>
            <h3>Blocked Cards</h3>
            <p class="stat-highlight" style="font-size:24px; font-weight:800; color:#dc2626; margin:6px 0;"><?= $blockedCards ?></p>
            <small>Suspended or flagged</small>
        </div>
        <div class="info-card">
            <div class="info-icon">📊</div>
            <h3>Total Transactions</h3>
            <p class="stat-highlight" style="font-size:24px; font-weight:800; color:#1d75f0; margin:6px 0;"><?= $totalTransactions ?></p>
            <small>Payments & Top-ups</small>
        </div>
    </div>

    <div class="quick-nav-grid" style="margin-top: 30px;">
        <div class="quick-nav-card" style="text-align: left;">
            <div class="card-icon">💳</div>
            <h3>Passenger Card Management</h3>
            <p style="margin-bottom: 18px;">Inspect passenger transit accounts, verify identification, and block or unblock cards.</p>
            <a href="unblockCard.php" class="btn-top-action" style="display:inline-block;">Open Card Manager</a>
        </div>

        <div class="quick-nav-card" style="text-align: left;">
            <div class="card-icon">💰</div>
            <h3>Payments & Transactions</h3>
            <p style="margin-bottom: 18px;">Verify ticket booking validity by Ticket ID and inspect station transaction history.</p>
            <a href="paymentHandling.php" class="btn-top-action" style="display:inline-block;">Inspect Payments</a>
        </div>
    </div>
</main>

<?php require_once('footer.php'); ?>
</body>
</html>
