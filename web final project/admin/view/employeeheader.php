<?php
 
$in_admin_view = (strpos($_SERVER['PHP_SELF'], '/admin/view/') !== false);
$home_path = $in_admin_view ? '../../employee/View/php/employeDashboard.php' : 'employeDashboard.php';
$profile_path = $in_admin_view ? 'Profile_Information.php' : '../../../admin/view/Profile_Information.php';
$logout_path = $in_admin_view ? '../../passenger/controller/LogoutController.php' : '../../../passenger/controller/LogoutController.php';
$logo_path = $in_admin_view ? 'signuplogo.png' : '../../../admin/view/signuplogo.png';
?>
 
<div class="header">
    <div class="header-left">
        <img src="<?php echo $logo_path; ?>" alt="Metro Logo" class="logo">
        <h1>Dhaka Metro</h1>
    </div>

    <div class="header-right">
        <a href="<?php echo $home_path; ?>">Home</a>
        <a href="<?php echo $profile_path; ?>">Profile</a>
        <a href="<?php echo $logout_path; ?>">Logout</a>
    </div>
</div>
