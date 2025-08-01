<?php
include("dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $state = $_POST["state"];
    $spotName = $_POST["spotName"];
    $description = $_POST["description"];
    $mapLink = $_POST["mapLink"];

    if (!$state || !$spotName || !$description || !$mapLink) {
        die("All fields are required.");
    }

    // Insert into database
    $sql = "INSERT INTO tourist_spots (state, name, description, map_link) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $state, $spotName, $description, $mapLink);
        if ($stmt->execute()) {
            // Append to the corresponding HTML page
            $htmlFile = ($state === "gujarat") ? "../gujarat.html" : "../rajasthan.html";

            // Create the new card HTML
            $cardHtml = "\n<div class='p-4 m-2 border rounded-xl bg-white shadow-md'>\n" .
                        "<h2 class='text-xl font-bold'>$spotName</h2>\n" .
                        "<p class='text-gray-700'>$description</p>\n" .
                        "<a href='$mapLink' target='_blank' class='text-blue-600 underline'>View on Google Maps</a>\n" .
                        "</div>\n";

            // Insert card just before closing </body> tag
            $contents = file_get_contents($htmlFile);
            if ($contents) {
                $updatedContents = str_replace("</body>", $cardHtml . "</body>", $contents);
                file_put_contents($htmlFile, $updatedContents);
            }

            echo "<script>
                alert('✅ Tourist spot added successfully!');
                window.location.href = '../admin-manage.html';
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

