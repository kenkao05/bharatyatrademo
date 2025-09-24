<?php
include("dbconnect.php");
$result = $conn->query("SELECT * FROM events ORDER BY start_date ASC");
$events = [];
while($row = $result->fetch_assoc()) {
    $events[] = $row;
}
header('Content-Type: application/json');
echo json_encode($events);
$conn->close();
?>
