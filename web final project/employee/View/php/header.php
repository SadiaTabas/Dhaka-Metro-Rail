<?php
$userName = $_SESSION['UserName'] ?? 'Staff Member';
?>
<header class="header">
    <div class="header-left">
        <a href="employeDashboard.php" style="display:flex; align-items:center; text-decoration:none;">
            <img src="../../../passenger/view/signuplogo.png" alt="Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>
    <div class="header-right">
        <span class="user-greeting">Logged in as: <strong><?= htmlspecialchars($userName) ?></strong></span>
        <a href="employeDashboard.php" class="nav-item">Dashboard</a>
        <a href="../../../admin/view/Profile_Information.php" class="nav-item">My Profile</a>
        <a href="../../../passenger/controller/LogoutController.php" class="nav-btn-logout">Logout</a>
    </div>
</header>
