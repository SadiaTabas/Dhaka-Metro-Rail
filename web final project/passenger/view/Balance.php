<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/PaymentModel.php";

header('Content-Type: application/json; charset=UTF-8');
$userID = (int)($_SESSION['UserID'] ?? 0);
$balance = getWalletBalance($userID);

echo json_encode(["balance" => (float)$balance]);
exit();
?>
