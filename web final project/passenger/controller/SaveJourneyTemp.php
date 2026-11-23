<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $from = trim($_POST['from'] ?? '');
    $to   = trim($_POST['to'] ?? '');
    $date = trim($_POST['journey_date'] ?? '');

    if ($from === "" || $to === "" || $date === "") {
        $_SESSION['error'] = "All fields are required!";
        header("Location: ../view/dashboard.php");
        exit();
    }

    if ($from === $to) {
        $_SESSION['error'] = "From and To stations cannot be the same!";
        header("Location: ../view/dashboard.php");
        exit();
    }

    // Save journey in session for after login
    $_SESSION['temp_journey'] = [
        'from' => $from,
        'to' => $to,
        'journey_date' => $date
    ];

    header("Location: ../../employee/View/php/login.php?redirect=journey");
    exit();
} else {
    header("Location: ../view/dashboard.php");
    exit();
}
?>
