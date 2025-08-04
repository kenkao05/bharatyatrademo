<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "tourist_db"; // 🔁 replace with your DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$from = $_POST['from_date'];
$to = $_POST['to_date'];

$sql = "SELECT * FROM events WHERE 
        (start_date BETWEEN ? AND ?) OR 
        (end_date BETWEEN ? AND ?) OR 
        (start_date <= ? AND end_date >= ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $from, $to, $from, $to, $from, $to);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events);

$conn->close();
?>
