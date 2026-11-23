<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: ../../employee/View/php/login.php?msg=logged_out");
exit();
?>
