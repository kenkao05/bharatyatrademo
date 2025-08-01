<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $state = $_POST["state"];
    $spot_name = $_POST["spot_name"];

    echo "State: $state<br>";
    echo "Spot Name: $spot_name<br>";

    $sql = "DELETE FROM tourist_spots WHERE state = ? AND name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $state, $spot_name);

    if ($stmt->execute()) {
        echo "✅ Tourist spot deleted successfully.";
    } else {
        echo "❌ Error deleting spot: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}

echo "<script>
    alert('✅ Tourist spot deleted successfully.');
    window.location.href = '../admin-manage.html';
</script>";


