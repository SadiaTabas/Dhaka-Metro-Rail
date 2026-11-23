<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Dhaka Metro Rail</title>
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
        <a href="../../../passenger/view/dashboard.php" class="nav-item">Home</a>
        <a href="login.php" class="nav-btn-login">Sign In</a>
    </div>
</header>

<main class="main-content">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Account Recovery</h2>
                <p>Enter your registered mobile number to reset password</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-box error-alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="../../Controller/forgotHandle.php" onsubmit="return validateForgot()">
                <div class="form-group">
                    <label for="phone">Registered Mobile Number</label>
                    <input type="text" id="phone" name="phone" placeholder="01XXXXXXXXX" maxlength="11" required>
                    <span id="phoneError" class="input-error-msg"></span>
                </div>

                <button type="submit" class="btn-submit">Verify Account</button>

                <div class="login-footer-links">
                    <p><a href="login.php">← Back to Login</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once('footer.php'); ?>
<script src="../js/forgot.js"></script>
</body>
</html>
