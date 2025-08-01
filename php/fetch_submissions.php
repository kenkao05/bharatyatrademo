<?php
include("dbconnect.php");
header('Content-Type: application/json');

$sql = "SELECT * FROM submissions ORDER BY id DESC";
$result = $conn->query($sql);
$submissions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
}

$conn->close();
echo json_encode($submissions);
?>
