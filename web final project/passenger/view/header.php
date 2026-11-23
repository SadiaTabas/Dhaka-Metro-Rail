<?php
$inPassengerView = (strpos($_SERVER['PHP_SELF'] ?? '', '/passenger/view/') !== false) || (strpos($_SERVER['REQUEST_URI'] ?? '', '/passenger/view/') !== false);
$homeLink    = $inPassengerView ? 'dashboard_after_login.php' : 'passenger/view/dashboard_after_login.php';
$profileLink = $inPassengerView ? 'payment_record_page.php' : 'passenger/view/payment_record_page.php';
$historyLink = $inPassengerView ? 'journey_history.php' : 'passenger/view/journey_history.php';
$logoutLink  = $inPassengerView ? '../controller/LogoutController.php' : 'passenger/controller/LogoutController.php';
$logoSrc     = $inPassengerView ? 'signuplogo.png' : 'passenger/view/signuplogo.png';
$cssPath     = $inPassengerView ? 'dashboard.css' : 'passenger/view/dashboard.css';
$userName    = $_SESSION['UserName'] ?? 'Passenger';
?>
<link rel="stylesheet" href="<?php echo $cssPath; ?>">
<header class="header">
    <div class="header-left">
        <a href="<?php echo $homeLink; ?>" style="display:flex; align-items:center; text-decoration:none;">
            <img src="<?php echo $logoSrc; ?>" alt="Dhaka Metro Logo" class="logo">
            <h1>Dhaka Metro</h1>
        </a>
    </div>

    <div class="header-right">
        <span class="user-greeting">Hi, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
        <a href="<?php echo $homeLink; ?>" class="nav-item">Book Journey</a>
        <a href="<?php echo $historyLink; ?>" class="nav-item">Journey History</a>
        <a href="<?php echo $profileLink; ?>" class="nav-item">My Account</a>
        <a href="<?php echo $logoutLink; ?>" class="nav-btn-logout">Logout</a>
    </div>
</header>
