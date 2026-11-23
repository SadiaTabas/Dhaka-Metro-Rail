<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

 
$_SESSION['from'] = $_POST['from'];
$_SESSION['to'] = $_POST['to'];
$_SESSION['journey_date'] = $_POST['journey_date'];

 
header("Location: /employee/View/php/login.php");
exit();
