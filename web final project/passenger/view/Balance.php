<?php
session_start();
include "../model/PaymentModel.php";

header('Content-Type: application/json; charset=UTF-8');
$userID = (int)($_SESSION['UserID'] ?? 0);
$balance = getWalletBalance($userID);

echo json_encode(["balance" => (float)$balance]);
exit();
?>
