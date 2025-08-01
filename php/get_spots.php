<?php
include("dbconnect.php");

if (isset($_GET['state'])) {
    $state = $_GET['state'];

    $sql = "SELECT name FROM tourist_spots WHERE state = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $state);
    $stmt->execute();
    $result = $stmt->get_result();

    $spots = [];
    while ($row = $result->fetch_assoc()) {
        $spots[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($spots);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([]);
}


