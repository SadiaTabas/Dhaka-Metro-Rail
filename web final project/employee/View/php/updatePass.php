<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
if (!isset($_SESSION['reset_mobile'])) {
    header("Location: forgot_password.php");
    exit();
}
$mobile = $_SESSION['reset_mobile'];
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../../../passenger/view/dashboard.css">
</head>
<body>

<header class="header">
    <div class="header-left">
        <a href="../../../passenger/view/dashboard.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="../../../passenger/view/signuplogo.png" alt="Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>
    <div class="header-right">
        <a href="login.php" class="nav-btn-login">Sign In</a>
    </div>
</header>

<main class="main-content">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Set New Password</h2>
                <p>Creating new password for <strong><?= htmlspecialchars($mobile) ?></strong></p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-box error-alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="../../Controller/updatePassHandle.php" method="post" onsubmit="return validatePassword()">
                <div class="form-group">
                    <label for="new_pass">New Password</label>
                    <input type="password" name="new_pass" id="new_pass" placeholder="At least 6 characters" required>
                </div>

                <div class="form-group">
                    <label for="confirm_pass">Confirm New Password</label>
                    <input type="password" name="confirm_pass" id="confirm_pass" placeholder="Re-enter password" required>
                    <span id="passError" class="input-error-msg"></span>
                </div>

                <button type="submit" class="btn-submit">Save New Password</button>

                <div class="login-footer-links">
                    <p><a href="login.php">Cancel and return to Login</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once('footer.php'); ?>
<script src="../js/updatePass.js"></script>
</body>
</html>
