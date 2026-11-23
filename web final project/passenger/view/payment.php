<?php
session_start();
include "../model/Database.php";
include "../model/PaymentModel.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userID = (int)$_SESSION['UserID'];
if (!isset($_GET['route_id'])) {
    header("Location: dashboard_after_login.php");
    exit();
}

$routeID = (int)$_GET['route_id'];
$journeyDate = $_GET['journey_date'] ?? date('Y-m-d');

// Fetch route details
$fromStation = "";
$toStation = "";
$fare = 0.00;

if ($conn) {
    $stmt = $conn->prepare("SELECT FromStation, ToStation, Fare FROM routes WHERE RouteID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $routeID);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $fromStation = $row['FromStation'];
            $toStation   = $row['ToStation'];
            $fare        = (float)$row['Fare'];
        }
        $stmt->close();
    }
}

$balance = getWalletBalance($userID);

$alert = $_SESSION['alert'] ?? '';
$alertSuccess = $_SESSION['alert_success'] ?? '';
unset($_SESSION['alert'], $_SESSION['alert_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journey Payment - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="payment.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<?php include "header.php"; ?>

<main class="main-content">
    <div class="payment-box">
        <div class="payment-form">
            <h2>Complete Ticket Payment</h2>
            <p class="payment-subtitle">Review your journey summary and confirm payment.</p>

            <?php if (!empty($alert)): ?>
                <div class="alert-box error-alert"><?= htmlspecialchars($alert) ?></div>
            <?php endif; ?>

            <?php if (!empty($alertSuccess)): ?>
                <div class="alert-box success-alert"><?= htmlspecialchars($alertSuccess) ?></div>
            <?php endif; ?>

            <div class="journey-summary-card">
                <div class="summary-route">
                    <span class="station-badge departure"><?= htmlspecialchars($fromStation ?: 'Selected Origin') ?></span>
                    <span class="route-arrow">➔</span>
                    <span class="station-badge destination"><?= htmlspecialchars($toStation ?: 'Selected Destination') ?></span>
                </div>
                <div class="summary-details">
                    <div class="summary-item">
                        <span class="label">Journey Date:</span>
                        <span class="value"><?= htmlspecialchars($journeyDate) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Ticket Fare:</span>
                        <span class="value fare-highlight"><?= number_format($fare, 2) ?> BDT</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Wallet Balance:</span>
                        <span class="value" id="balanceSpan"><?= number_format($balance, 2) ?> BDT</span>
                    </div>
                </div>
            </div>

            <form id="paymentForm" method="POST" action="../controller/PaymentController.php">
                <input type="hidden" name="route_id" value="<?= $routeID ?>">
                <input type="hidden" name="amount" id="amount" value="<?= $fare ?>">
                <input type="hidden" name="journey_date" value="<?= htmlspecialchars($journeyDate) ?>">

                <div class="form-group">
                    <label for="payment_method">Select Payment Method</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">-- Choose Payment Option --</option>
                        <option value="MRT Transit Wallet" <?= ($balance >= $fare) ? 'selected' : '' ?>>MRT Transit Wallet (Balance: <?= number_format($balance, 2) ?> BDT)</option>
                        <option value="Bkash">bKash Mobile Banking</option>
                        <option value="Nagad">Nagad Mobile Banking</option>
                        <option value="Rocket">DBBL Rocket</option>
                        <option value="Visa/MasterCard">Debit / Credit Card</option>
                    </select>
                </div>

                <div class="payment-disclaimer">
                    <p>✓ Instant ticket confirmation • Safe digital payment • Terms & Conditions apply</p>
                </div>

                <div class="btn-group">
                    <button type="submit" name="submit" class="btn-pay">Confirm & Pay <?= number_format($fare, 2) ?> BDT</button>
                    <a href="dashboard_after_login.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include "footer.php"; ?>
<script src="payment.js"></script>
</body>
</html>
