<?php
// delete_submission.php

$host = "localhost";
$user = "root";
$pass = "";
$db = "bharatyatra";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Get image path before deleting
    $res = $conn->query("SELECT image_path FROM suggestions WHERE id=$id");

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();

        // Delete the image file if it exists
        if (!empty($row['image_path']) && file_exists($row['image_path'])) {
            unlink($row['image_path']);
        }
    }

    // Delete the database record
    $del = $conn->query("DELETE FROM suggestions WHERE id=$id");

    if ($del) {
        echo "Submission deleted successfully!";
    } else {
        echo "Error: Could not delete submission.";
    }
} else {
    echo "Error: Invalid submission ID.";
}

$conn->close();
?>