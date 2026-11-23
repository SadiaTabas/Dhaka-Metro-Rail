<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
$error = $_SESSION['signup_error'] ?? '';
unset($_SESSION['signup_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Registration - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<header class="header">
    <div class="header-left">
        <a href="dashboard.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="signuplogo.png" alt="Dhaka Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>
    <div class="header-right">
        <a href="dashboard.php" class="nav-item">Home</a>
        <a href="../../employee/View/php/login.php" class="nav-btn-login">Login</a>
    </div>
</header>

<main class="main-content">
    <div class="signup-container">
        <div class="signup-box">
            <h2>Create Passenger Account</h2>
            <p class="signup-subtitle">Register to book tickets, manage transit wallet, and track journeys.</p>

            <?php if (!empty($error)): ?>
                <div class="alert-box error-alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="../controller/SignupController.php" onsubmit="return validateForm();">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" id="fullname" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" id="mobile" placeholder="01XXXXXXXXX (11 digits)" maxlength="11" required>
                </div>

                <div class="form-group">
                    <label>NID Number</label>
                    <input type="text" name="nid" id="nid" placeholder="National ID number" required>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="dob" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group form-col">
                        <label>Gender</label>
                        <select name="gender" id="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="At least 6 characters" required>
                        <button type="button" class="btn-toggle-pwd" onclick="togglePwd('password')">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter password" required>
                        <button type="button" class="btn-toggle-pwd" onclick="togglePwd('confirm_password')">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Complete Registration</button>
            </form>

            <div class="login-link">
                Already registered? <a href="../../employee/View/php/login.php">Sign In Here</a>
            </div>
        </div>
    </div>
</main>

<?php include "footer.php"; ?>
<script src="script.js"></script>
<script>
function togglePwd(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
