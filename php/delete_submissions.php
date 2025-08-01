<?php
include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    $stmt = $conn->prepare("DELETE FROM submissions WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "fail: execute error";
        }
        $stmt->close();
    } else {
        echo "fail: prepare error";
    }

    $conn->close();
} else {
    echo "fail: invalid request";
}
?>


