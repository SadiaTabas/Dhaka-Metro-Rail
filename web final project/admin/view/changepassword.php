<?php
session_start();

if (isset($_SESSION['userid'])) {
    $_SESSION['Mobile'] = $_SESSION['userid'];  
}

if (!isset($_SESSION['Mobile'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$roleID = isset($_SESSION['RoleID']) ? (int)$_SESSION['RoleID'] : 1;
if ($roleID === 1) {
    include "adminheader.php";
    $cancelUrl = "admindashboard.php";
} elseif ($roleID === 2) {
    include "employeeheader.php";
    $cancelUrl = "../../employee/View/php/employeDashboard.php";
} elseif ($roleID === 3) {
    include "userheader.php";
    $cancelUrl = "../../passenger/view/dashboard_after_login.php";
} else {
    include "adminheader.php";
    $cancelUrl = "admindashboard.php";
}

$mobile = trim($_SESSION['Mobile']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="changepassword.css">
    <link rel="stylesheet" href="../../passenger/view/dashboard.css">
</head>
<body>

<main class="main-content">
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h2>Change Account Password</h2>
                <p>Updating credentials for user <strong><?php echo htmlspecialchars($mobile); ?></strong></p>
            </div>

            <?php
            if (isset($_GET['msg'])) {
                $m = $_GET['msg'];
                if ($m === 'wrong') echo "<div class='alert-box error-alert'>Current password is incorrect.</div>";
                if ($m === 'nomatch') echo "<div class='alert-box error-alert'>New password and confirm password do not match.</div>";
                if ($m === 'empty') echo "<div class='alert-box error-alert'>All fields are required.</div>";
                if ($m === 'length') echo "<div class='alert-box error-alert'>New password must be at least 6 characters long.</div>";
                if ($m === 'usernotfound') echo "<div class='alert-box error-alert'>User record not found.</div>";
                if ($m === 'error') echo "<div class='alert-box error-alert'>Failed to update password. Please try again.</div>";
            }
            ?>

            <form name="changePasswordForm" method="post" action="../controller/ChangePasswordController.php" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="At least 6 characters" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter new password" required>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="btn-submit" style="flex:2;">Update Password</button>
                    <a href="<?php echo $cancelUrl; ?>" class="btn-cancel" style="flex:1; margin-top:10px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="changepassword.js"></script>
<?php include "footer.php"; ?>
</body>
</html>
