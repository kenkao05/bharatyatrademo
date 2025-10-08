<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "bharatyatra";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle single submission fetch (for modal view)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM suggestions WHERE id = $id";
    $result = $conn->query($sql);

    if ($row = $result->fetch_assoc()) {
        $imagePath = !empty($row['image_path']) ? $row['image_path'] : '';
        $hasImage = $imagePath && file_exists($imagePath);

        echo "<div class='detail-row'>
                <div class='detail-label'>Submission ID</div>
                <div class='detail-value'>#{$row['id']}</div>
              </div>";

        echo "<div class='detail-row'>
                <div class='detail-label'>Spot Name</div>
                <div class='detail-value'>{$row['spot_name']}</div>
              </div>";

        echo "<div class='detail-row'>
                <div class='detail-label'>State</div>
                <div class='detail-value'>{$row['spot_state']}</div>
              </div>";

        echo "<div class='detail-row'>
                <div class='detail-label'>District</div>
                <div class='detail-value'>{$row['spot_district']}</div>
              </div>";

        echo "<div class='detail-row'>
                <div class='detail-label'>Description</div>
                <div class='detail-value'>{$row['description']}</div>
              </div>";

        if ($hasImage) {
            echo "<div class='detail-row'>
                    <div class='detail-label'>Image</div>
                    <img src='{$imagePath}' alt='{$row['spot_name']}' class='modal-image'>
                  </div>";
        } else {
            echo "<div class='detail-row'>
                    <div class='detail-label'>Image</div>
                    <div class='no-image' style='height:150px;'>📷 No image uploaded</div>
                  </div>";
        }

        echo "<div class='detail-row'>
                <div class='detail-label'>Submitted On</div>
                <div class='detail-value'>" . date('F d, Y \a\t g:i A', strtotime($row['created_at'])) . "</div>
              </div>";
    } else {
        echo "<p>No record found.</p>";
    }
    exit;
}

// Handle all submissions (for grid)
$filter = "";
if (isset($_GET['state']) && $_GET['state'] !== "") {
    $state = $conn->real_escape_string($_GET['state']);
    $filter = "WHERE spot_state = '$state'";
}

$sql = "SELECT * FROM suggestions $filter ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagePath = !empty($row['image_path']) ? $row['image_path'] : '';
        $hasImage = $imagePath && file_exists($imagePath);

        echo "
        <div class='submission-card' data-state='{$row['spot_state']}'>
          <div class='card-header'>
            <span class='card-id'>#{$row['id']}</span>
            <span class='card-date'>" . date('M d, Y', strtotime($row['created_at'])) . "</span>
          </div>
          
          <h3 class='spot-title'>{$row['spot_name']}</h3>
          <div class='spot-location'>
            <span class='location-badge'>{$row['spot_state']}</span>
            <span class='location-badge'>{$row['spot_district']}</span>
          </div>
          
          <p class='spot-description'>" . substr($row['description'], 0, 150) . "...</p>
          
          <div class='image-section'>";

        if ($hasImage) {
            echo "<img src='{$imagePath}' alt='{$row['spot_name']}'>";
        } else {
            echo "<div class='no-image'>📷 No image uploaded</div>";
        }

        echo "</div>
          
          <div class='card-actions'>
            <button class='btn btn-view' onclick='viewDetails({$row['id']})'>View Details</button>";

        if ($hasImage) {
            echo "<a href='{$imagePath}' download class='btn btn-download'>⬇ Image</a>";
        }

        echo "
            <button class='btn btn-delete' onclick='deleteSubmission({$row['id']})'>Delete</button>
          </div>
        </div>
        ";
    }
} else {
    echo "<div class='empty-state'>
            <div class='empty-state-icon'>📭</div>
            <h3>No submissions yet</h3>
            <p>Travel spot suggestions will appear here</p>
          </div>";
}

$conn->close();
?>