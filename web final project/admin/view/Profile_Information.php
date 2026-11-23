<?php
session_start();

if (!isset($_SESSION['UserID'], $_SESSION['RoleID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

include "../model/Profile_Information.php";

$userId = (int)$_SESSION['UserID'];
$roleID = (int)$_SESSION['RoleID'];

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
    header("Location: ../../employee/View/php/login.php");
    exit();
}

$result  = getUserProfile($userId);
$profile = $result ? mysqli_fetch_assoc($result) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Information - Dhaka Metro Rail</title>
    <link rel="stylesheet" href="Profile_Information.css">
    <link rel="stylesheet" href="../../passenger/view/dashboard.css">
</head>
<body>

<main class="main-content">
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <h2>Account Profile Information</h2>
                <p>View and update your personal account details</p>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                <div class="alert-box success-alert">Profile information updated successfully.</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'empty'): ?>
                <div class="alert-box error-alert">Full name is required.</div>
            <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'error'): ?>
                <div class="alert-box error-alert">Failed to update profile. Please try again.</div>
            <?php endif; ?>

            <form name="profileForm" id="profileForm" method="post" action="../controller/Profile_Information_Controller.php" onsubmit="return validateForm()">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars(($profile['FirstName'] ?? '') . ' ' . ($profile['LastName'] ?? '')); ?>" required>
                </div>

                <div class="form-group">
                    <label>National ID (NID)</label>
                    <input type="text" value="<?php echo htmlspecialchars($profile['NID'] ?? 'Not Provided'); ?>" readonly class="input-readonly">
                    <small style="color:#64748b; font-size:12px;">NID cannot be changed online for security.</small>
                </div>

                <div class="form-row">
                    <div class="form-group form-col">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($profile['DateOfBirth'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group form-col">
                        <label>Gender</label>
                        <select name="gender" id="gender" required>
                            <option value="Male"   <?php if (($profile['Gender'] ?? '') === "Male") echo "selected"; ?>>Male</option>
                            <option value="Female" <?php if (($profile['Gender'] ?? '') === "Female") echo "selected"; ?>>Female</option>
                            <option value="Other"  <?php if (($profile['Gender'] ?? '') === "Other") echo "selected"; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" value="<?php echo htmlspecialchars($profile['Mobile'] ?? ''); ?>" readonly class="input-readonly">
                    <small style="color:#64748b; font-size:12px;">Mobile number serves as your unique login ID.</small>
                </div>

                <div class="profile-actions">
                    <button type="submit" class="btn-submit" style="flex:2;">Save Profile Changes</button>
                    <a href="changepassword.php" class="btn-top-action" style="flex:1; text-align:center; padding:14px; text-decoration:none; margin-top:10px;">Change Password</a>
                    <a href="<?php echo $cancelUrl; ?>" class="btn-cancel" style="flex:1; margin-top:10px;">Return</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include "footer.php"; ?>
<script src="Profile_Information.js"></script>
</body>
</html>
