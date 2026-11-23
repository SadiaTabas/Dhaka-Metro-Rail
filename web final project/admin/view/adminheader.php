<?php
$userName = $_SESSION['UserName'] ?? 'Administrator';
?>
<link rel="stylesheet" href="../../passenger/view/dashboard.css">
<header class="header">
    <div class="header-left">
        <a href="admindashboard.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="../../passenger/view/signuplogo.png" alt="Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>

    <div class="header-right">
        <span class="user-greeting">Admin: <strong><?php echo htmlspecialchars($userName); ?></strong></span>
        <a href="admindashboard.php" class="nav-item">Dashboard</a>
        <a href="Profile_Information.php" class="nav-item">My Profile</a>
        <a href="../../passenger/controller/LogoutController.php" class="nav-btn-logout">Logout</a>
    </div>
</header>
