<?php
$inAdminView = (strpos($_SERVER['PHP_SELF'] ?? '', '/admin/view/') !== false);
$homePath = $inAdminView ? '../../employee/View/php/employeDashboard.php' : 'employeDashboard.php';
$profilePath = $inAdminView ? 'Profile_Information.php' : '../../../admin/view/Profile_Information.php';
$logoutPath = $inAdminView ? '../../passenger/controller/LogoutController.php' : '../../../passenger/controller/LogoutController.php';
$logoPath = $inAdminView ? '../../passenger/view/signuplogo.png' : '../../../passenger/view/signuplogo.png';
$userName = $_SESSION['UserName'] ?? 'Staff Member';
?>
<link rel="stylesheet" href="<?php echo $inAdminView ? '../../passenger/view/dashboard.css' : '../../../passenger/view/dashboard.css'; ?>">
<header class="header">
    <div class="header-left">
        <a href="<?php echo $homePath; ?>" style="display:flex; align-items:center; text-decoration:none;">
            <img src="<?php echo $logoPath; ?>" alt="Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>

    <div class="header-right">
        <span class="user-greeting">Staff: <strong><?php echo htmlspecialchars($userName); ?></strong></span>
        <a href="<?php echo $homePath; ?>" class="nav-item">Dashboard</a>
        <a href="<?php echo $profilePath; ?>" class="nav-item">Profile</a>
        <a href="<?php echo $logoutPath; ?>" class="nav-btn-logout">Logout</a>
    </div>
</header>
