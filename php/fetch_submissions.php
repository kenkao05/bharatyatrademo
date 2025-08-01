<?php
header('Content-Type: application/json');
include("dbconnect.php");

$sql = "SELECT * FROM submissions ORDER BY id DESC";
$result = $conn->query($sql);
$submissions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }
} else {
    echo json_encode(["error" => $conn->error]);
    exit();
}

$conn->close();
echo json_encode($submissions);
?>
