<?php
$inAdminView = (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/view/') !== false);
$homePath = $inAdminView ? '../../passenger/view/dashboard_after_login.php' : '../view/dashboard_after_login.php';
$profilePath = $inAdminView ? 'Profile_Information.php' : 'Profile_Information.php';
$logoutPath = $inAdminView ? '../../passenger/controller/LogoutController.php' : '../controller/LogoutController.php';
$logoPath = $inAdminView ? '../../passenger/view/signuplogo.png' : 'signuplogo.png';
$userName = $_SESSION['UserName'] ?? 'Passenger';
?>
<link rel="stylesheet" href="<?php echo $inAdminView ? '../../passenger/view/dashboard.css' : 'dashboard.css'; ?>">
<header class="header">
    <div class="header-left">
        <a href="<?php echo $homePath; ?>" style="display:flex; align-items:center; text-decoration:none;">
            <img src="<?php echo $logoPath; ?>" alt="Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>

    <div class="header-right">
        <span class="user-greeting">Passenger: <strong><?php echo htmlspecialchars($userName); ?></strong></span>
        <a href="<?php echo $homePath; ?>" class="nav-item">Home</a>
        <a href="<?php echo $profilePath; ?>" class="nav-item">Profile</a>
        <a href="<?php echo $logoutPath; ?>" class="nav-btn-logout">Logout</a>
    </div>
</header>
