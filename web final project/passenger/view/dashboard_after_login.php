<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/Database.php";
include "../model/PaymentModel.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$userid = (int)$_SESSION['UserID'];

$from = $to = $date = '';
if (isset($_SESSION['temp_journey'])) {
    $from = $_SESSION['temp_journey']['from'] ?? '';
    $to   = $_SESSION['temp_journey']['to'] ?? '';
    $date = $_SESSION['temp_journey']['journey_date'] ?? date('Y-m-d');
} else {
    $date = date('Y-m-d');
}

$username = $_SESSION['UserName'] ?? "Passenger";
if ($conn) {
    $stmt = $conn->prepare("SELECT FirstName, LastName FROM users WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            $username = trim($row['FirstName'] . " " . $row['LastName']);
            $_SESSION['UserName'] = $username;
        }
        $stmt->close();
    }
}

$walletBalance = getWalletBalance($userid);

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$stations = [
    "Uttara North", "Uttara Center", "Uttara South", "Pallabi",
    "Mirpur-11", "Mirpur-10", "Kazipara", "Shewrapara",
    "Agargaon", "Bijoy Sarani", "Farmgate", "Karwan Bazar",
    "Shahbagh", "Dhaka University", "Secretariat", "Motijheel", "Kamalapur"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<?php include "header.php"; ?>

<main class="main-content">
    <div class="user-welcome-bar">
        <div class="welcome-left">
            <h2>Welcome, <?= htmlspecialchars($username) ?></h2>
            <p>Plan your trip on Dhaka Metro MRT Line-6</p>
        </div>
        <div class="welcome-right">
            <div class="wallet-badge">
                <span class="wallet-label">Wallet Balance</span>
                <span class="wallet-amount"><?= number_format($walletBalance, 2) ?> BDT</span>
            </div>
        </div>
    </div>

    <div class="dashboard-box" id="booking">
        <div class="form-side">
            <h2>Book Your Journey</h2>
            <p class="form-subtitle">Choose your origin and destination stations below.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-box error-alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="journeyForm" method="post" action="../controller/DashboardSubmit.php">
                <div class="form-group">
                    <label for="from">From Station</label>
                    <select name="from" id="from" required>
                        <option value="">-- Select Departure Station --</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= ($from === $st) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="to">To Station</label>
                    <select name="to" id="to" required>
                        <option value="">-- Select Destination Station --</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= ($to === $st) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="journey_date">Date of Journey</label>
                    <input type="date" name="journey_date" id="journey_date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($date) ?>" required>
                </div>

                <button type="submit" class="btn-submit">Continue to Payment</button>
            </form>
        </div>

        <div class="image-side">
            <img src="dashboard1.jpg" alt="Dhaka Metro Train">
            <div class="train-badge">Quick Digital Ticketing</div>
        </div>
    </div>

    <div class="quick-nav-grid">
        <a href="journey_history.php" class="quick-nav-card">
            <div class="card-icon">🎫</div>
            <h3>Journey History</h3>
            <p>View your past journeys and booked tickets</p>
        </a>
        <a href="payment_record_page.php?type=tickets" class="quick-nav-card">
            <div class="card-icon">📑</div>
            <h3>Ticket History</h3>
            <p>Check confirmation status of all tickets</p>
        </a>
        <a href="payment_record_page.php?type=payments" class="quick-nav-card">
            <div class="card-icon">💳</div>
            <h3>Payment Records</h3>
            <p>View transaction receipts and history</p>
        </a>
        <a href="../../admin/view/Profile_Information.php" class="quick-nav-card">
            <div class="card-icon">👤</div>
            <h3>Profile Settings</h3>
            <p>Update personal information & password</p>
        </a>
    </div>
</main>

<?php include "footer.php"; ?>
<script src="dashboard.js"></script>
</body>
</html>
