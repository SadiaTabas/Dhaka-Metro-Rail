<?php
include "../model/Database.php";

$routeID = (int)($_GET['route_id'] ?? 0);
$fare = 0;

if ($conn && $routeID > 0) {
    $stmt = $conn->prepare("SELECT Fare FROM routes WHERE RouteID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $routeID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $fare = (float)$row['Fare'];
        }
        $stmt->close();
    }
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(["fare" => $fare]);
exit();
?>
