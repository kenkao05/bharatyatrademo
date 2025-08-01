<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $state = $_POST["state"];
    $spotName = $_POST["spotName"];
    $description = $_POST["description"];
    $mapLink = $_POST["mapLink"];

    $sql = "INSERT INTO submissions (state, spotName, description, mapLink) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $state, $spotName, $description, $mapLink);
        if ($stmt->execute()) {
            echo "<script>
                alert('✅ Form submitted successfully!');
                window.location.href = '../userform.html';
            </script>";
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
