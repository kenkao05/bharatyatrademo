<?php
include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET["state"])) {
        die("State is required.");
    }

    $state = $_GET["state"];

    $sql = "SELECT name, description, map_link FROM tourist_spots WHERE state = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $state);
        $stmt->execute();
        $result = $stmt->get_result();

        $spots = array();
        while ($row = $result->fetch_assoc()) {
            $spots[] = $row;
        }

        echo json_encode($spots);
    } else {
        echo "Prepare failed: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
