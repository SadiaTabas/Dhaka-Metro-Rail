<?php
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$isRootRequest = ($reqPath === '/' || $reqPath === '/index.php' || empty(trim($reqPath, '/')));

$homeLink     = $isRootRequest ? '/' : 'dashboard.php';
$registerLink = $isRootRequest ? 'passenger/view/signup.php' : 'signup.php';
$loginLink    = $isRootRequest ? 'employee/View/php/login.php' : '../../employee/View/php/login.php';
$logoSrc      = $isRootRequest ? 'passenger/view/signuplogo.png' : 'signuplogo.png';
$cssPath      = $isRootRequest ? 'passenger/view/dashboard.css' : 'dashboard.css';
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
