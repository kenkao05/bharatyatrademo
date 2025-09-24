<?php
include("dbconnect.php");
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $name = $_POST['name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?: null;
    $state = $_POST['state'];
    $description = $_POST['description'];
    $wikipedia_link = $_POST['wikipedia_link'];

    $stmt = $conn->prepare("INSERT INTO events (name,start_date,end_date,state,description,wikipedia_link) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss",$name,$start_date,$end_date,$state,$description,$wikipedia_link);
    if($stmt->execute()){
        echo "Event added successfully ✅";
    } else { echo "Error: ".$stmt->error; }
    $stmt->close();
    $conn->close();
}
?>
