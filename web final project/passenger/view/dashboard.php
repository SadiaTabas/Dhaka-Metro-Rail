<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// If passenger is already logged in, forward to authenticated dashboard
if (isset($_SESSION['UserID']) && ($_SESSION['RoleID'] ?? 0) == 3) {
    $reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $target = (strpos($reqPath, '/passenger/view/') !== false) ? 'dashboard_after_login.php' : 'passenger/view/dashboard_after_login.php';
    header("Location: $target");
    exit();
}

$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isRootRequest = ($reqPath === '/' || $reqPath === '/index.php' || empty(trim($reqPath, '/')));

$formAction   = $isRootRequest ? 'passenger/controller/SaveJourneyTemp.php' : '../controller/SaveJourneyTemp.php';
$imgSrc       = $isRootRequest ? 'passenger/view/dashboard1.jpg' : 'dashboard1.jpg';
$jsSrc        = $isRootRequest ? 'passenger/view/dashboard.js' : 'dashboard.js';
$cssSrc       = $isRootRequest ? 'passenger/view/dashboard.css' : 'dashboard.css';

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
    <title>Dhaka Metro Rail - Fast, Safe & Green Urban Transit</title>
    <link rel="stylesheet" href="<?php echo $cssSrc; ?>">
</head>
<body>

<?php include __DIR__ . "/dashboardbeforeheader.php"; ?>

<main class="main-content">
    <div class="hero-banner">
        <h2>Dhaka Metro Rail (MRT Line-6)</h2>
        <p>Connecting Uttara to Motijheel • Fast, Reliable & Eco-Friendly Travel</p>
    </div>

    <div class="dashboard-box">
        <div class="form-side">
            <h2>Book Your Journey</h2>
            <p class="form-subtitle">Select origin and destination stations to calculate fare and book tickets.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-box error-alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="journeyForm" method="post" action="<?php echo $formAction; ?>">
                <div class="form-group">
                    <label for="from">From Station</label>
                    <select name="from" id="from" required>
                        <option value="">-- Select Departure Station --</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="to">To Station</label>
                    <select name="to" id="to" required>
                        <option value="">-- Select Destination Station --</option>
                        <?php foreach ($stations as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>"><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="journey_date">Date of Journey</label>
                    <input type="date" name="journey_date" id="journey_date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
                </div>

                <button type="submit" class="btn-submit">Proceed to Booking</button>
            </form>
        </div>

        <div class="image-side">
            <img src="<?php echo $imgSrc; ?>" alt="Dhaka Metro Train">
            <div class="train-badge">MRT Line-6 Express</div>
        </div>
    </div>

    <div class="info-cards-container">
        <div class="info-card">
            <div class="info-icon">🕒</div>
            <h3>Operating Hours</h3>
            <p>07:10 AM - 09:40 PM<br><small>Saturday to Thursday</small></p>
        </div>
        <div class="info-card">
            <div class="info-icon">🚉</div>
            <h3>Active Stations</h3>
            <p>17 Operational Stations<br><small>Uttara North to Motijheel</small></p>
        </div>
        <div class="info-card">
            <div class="info-icon">💳</div>
            <h3>Standard Fares</h3>
            <p>BDT 20 to BDT 100<br><small>MRT Pass & Single Journey</small></p>
        </div>
    </div>
</main>

<?php include __DIR__ . "/footer.php"; ?>
<script src="<?php echo $jsSrc; ?>"></script>
</body>
</html>
