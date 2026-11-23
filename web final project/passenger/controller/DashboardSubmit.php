<?php
if (session_status() === PHP_SESSION_NONE) {
    if (!@is_writable(session_save_path())) {
        @session_save_path(sys_get_temp_dir());
    }
    session_start();
}
include "../model/Database.php";

$redirectPage = isset($_SESSION['UserID'])
    ? "../view/dashboard_after_login.php"
    : "../view/dashboard.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $from = trim($_POST['from'] ?? '');
    $to   = trim($_POST['to'] ?? '');
    $date = trim($_POST['journey_date'] ?? '');

    if ($from === "" || $to === "" || $date === "") {
        $_SESSION['error'] = "All fields are required!";
        header("Location: $redirectPage");
        exit();
    }

    if ($from === $to) {
        $_SESSION['error'] = "From and To stations cannot be the same!";
        header("Location: $redirectPage");
        exit();
    }

    if (!$conn) {
        $_SESSION['error'] = "Database not connected. Please check configuration.";
        header("Location: $redirectPage");
        exit();
    }

    // Look for existing route
    $stmt = $conn->prepare("SELECT RouteID, Fare FROM routes WHERE FromStation = ? AND ToStation = ?");
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $route = $result->fetch_assoc();
        $routeID = $route['RouteID'];
        $stmt->close();
    } else {
        $stmt->close();
        // Station order on MRT Line-6
        $allStations = [
            'Uttara North', 'Uttara Center', 'Uttara South', 'Pallabi',
            'Mirpur-11', 'Mirpur-10', 'Kazipara', 'Shewrapara',
            'Agargaon', 'Bijoy Sarani', 'Farmgate', 'Karwan Bazar',
            'Shahbagh', 'Dhaka University', 'Secretariat', 'Motijheel', 'Kamalapur'
        ];
        $i1 = array_search($from, $allStations);
        $i2 = array_search($to, $allStations);

        if ($i1 !== false && $i2 !== false) {
            $dist = abs($i1 - $i2);
            $fare = min(100.00, max(20.00, 20.00 + ($dist - 1) * 10.00));
        } else {
            $fare = 50.00; // fallback standard fare
        }

        // Insert new route
        $insStmt = $conn->prepare("INSERT INTO routes (FromStation, ToStation, Fare) VALUES (?, ?, ?)");
        if ($insStmt) {
            $insStmt->bind_param("ssd", $from, $to, $fare);
            $insStmt->execute();
            $routeID = $insStmt->insert_id;
            $insStmt->close();
        } else {
            $_SESSION['error'] = "Could not create route. Please try again.";
            header("Location: $redirectPage");
            exit();
        }
    }

    // Redirect to payment page
    header("Location: ../view/payment.php?route_id=$routeID&journey_date=" . urlencode($date));
    exit();

} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $redirectPage");
    exit();
}
?>
