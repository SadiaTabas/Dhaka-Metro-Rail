<?php
// Context-aware paths for navigation
$inPassengerView = (strpos($_SERVER['PHP_SELF'] ?? '', '/passenger/view/') !== false) || (strpos($_SERVER['REQUEST_URI'] ?? '', '/passenger/view/') !== false);
$homeLink     = $inPassengerView ? 'dashboard.php' : './';
$registerLink = $inPassengerView ? 'signup.php' : 'passenger/view/signup.php';
$loginLink    = $inPassengerView ? '../../employee/View/php/login.php' : 'employee/View/php/login.php';
$logoSrc      = $inPassengerView ? 'signuplogo.png' : 'passenger/view/signuplogo.png';
$cssPath      = $inPassengerView ? 'dashboard.css' : 'passenger/view/dashboard.css';
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
        <a href="<?php echo $homeLink; ?>" class="nav-item">Home</a>
        <a href="<?php echo $registerLink; ?>" class="nav-item">Register</a>
        <a href="<?php echo $loginLink; ?>" class="nav-btn-login">Login</a>
    </div>
</header>
