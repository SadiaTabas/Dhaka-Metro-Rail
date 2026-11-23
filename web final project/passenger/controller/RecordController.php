<?php
session_start();
include "../model/RecordModel.php";

if (!isset($_SESSION['UserID'])) {
    header("Location: ../../employee/View/php/login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id <= 0) {
        header("Location: ../view/payment_record_page.php?type=payments&error=invalid");
        exit();
    }

    deletePayment($id);
    header("Location: ../view/payment_record_page.php?type=payments&msg=deleted");
    exit();
} else {
    header("Location: ../view/payment_record_page.php");
    exit();
}
?>
