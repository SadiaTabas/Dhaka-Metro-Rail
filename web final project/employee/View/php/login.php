<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

// If already logged in, redirect to respective dashboard
if (isset($_SESSION['UserID'], $_SESSION['RoleID'])) {
    if ($_SESSION['RoleID'] == 1) {
        header("Location: ../../../admin/view/admindashboard.php");
        exit();
    } elseif ($_SESSION['RoleID'] == 2) {
        header("Location: employeDashboard.php");
        exit();
    } elseif ($_SESSION['RoleID'] == 3) {
        header("Location: ../../../passenger/view/dashboard_after_login.php");
        exit();
    }
}

$userIdError   = $_SESSION['userIdError'] ?? '';
$passwordError = $_SESSION['passwordError'] ?? '';
$userIdValue   = $_SESSION['userIdValue'] ?? '';
$signupSuccess = $_SESSION['signup_success'] ?? '';

unset($_SESSION['userIdError'], $_SESSION['passwordError'], $_SESSION['userIdValue'], $_SESSION['signup_success']);

$msg = $_GET['msg'] ?? '';
if ($msg === 'registered') {
    $signupSuccess = "Registration successful! Please login with your mobile number and password.";
} elseif ($msg === 'PasswordUpdated') {
    $signupSuccess = "Password reset successfully! Please sign in with your new password.";
} elseif ($msg === 'logged_out') {
    $signupSuccess = "You have been logged out successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Dhaka Metro Rail</title>
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
        <a href="../../../passenger/view/signup.php" class="nav-item">Register</a>
    </div>
</header>

<main class="main-content">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h2>Account Sign In</h2>
                <p>Access Passenger, Staff, or Administrative Services</p>
            </div>

            <?php if (!empty($signupSuccess)): ?>
                <div class="alert-box success-alert">
                    <?= htmlspecialchars($signupSuccess) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="../../Controller/loginHandle.php" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="phone">Registered Mobile Number</label>
                    <input
                        type="text"
                        id="phone"
                        name="userid"
                        placeholder="01XXXXXXXXX"
                        maxlength="11"
                        value="<?php echo htmlspecialchars($userIdValue); ?>"
                        required
                    >
                    <span id="phoneError" class="input-error-msg"><?php echo $userIdError; ?></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your account password"
                            required
                        >
                        <button type="button" class="btn-toggle-pwd" onclick="togglePwd('password')">👁️</button>
                    </div>
                    <span id="passwordError" class="input-error-msg"><?php echo $passwordError; ?></span>
                </div>

                <div class="form-options">
                    <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-submit">Sign In to Account</button>

                <div class="login-footer-links">
                    <p>New passenger? <a href="../../../passenger/view/signup.php">Create an Account</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
<script src="../js/loginHandle.js"></script>
<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
