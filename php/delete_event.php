<?php
include("dbconnect.php");
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $id = $_POST['event_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt->bind_param("i",$id);
    if($stmt->execute()){
        echo "Event deleted successfully ✅";
    } else { echo "Error: ".$stmt->error; }
    $stmt->close();
    $conn->close();
}
?>