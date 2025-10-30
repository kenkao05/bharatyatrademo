<?php
// userform.php - receive suggestions from userform.html
// Basic secure handling: prepared statements, image validation, uploads directory creation


error_reporting(E_ALL);
ini_set('display_errors', 1);


$host = "localhost";
$user = "root";
$pass = "";
$db = "bharatyatra";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // Redirect back with error
    $msg = urlencode('DB connection failed');
    header("Location: userform.html?status=error&msg={$msg}");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic required fields
    $spot_name = isset($_POST['spot_name']) ? trim($_POST['spot_name']) : '';
    $spot_state = isset($_POST['spot_state']) ? trim($_POST['spot_state']) : '';
    $spot_district = isset($_POST['spot_district']) ? trim($_POST['spot_district']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    if ($spot_name === '' || $spot_state === '' || $spot_district === '' || $description === '') {
        $msg = urlencode('Missing required fields');
        header("Location: userform.html?status=error&msg={$msg}");
        exit();
    }

    // Handle image upload if provided
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $maxBytes = 10 * 1024 * 1024; // 10 MB
            $origName = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];
            $size = $_FILES['image']['size'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if ($size > $maxBytes) {
                $msg = urlencode('Uploaded image too large (max 10MB)');
                header("Location: userform.html?status=error&msg={$msg}");
                exit();
            }
            if (!in_array($ext, $allowed)) {
                $msg = urlencode('Invalid image type');
                header("Location: userform.html?status=error&msg={$msg}");
                exit();
            }

            // Ensure uploads dir exists and is writable
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Try to make writable if not
            if (!is_writable($uploadDir)) {
                @chmod($uploadDir, 0775);
            }

            if (!is_writable($uploadDir)) {
                $msg = urlencode('Uploads directory not writable: ' . $uploadDir);
                error_log('Upload error: uploads directory not writable: ' . $uploadDir);
                header("Location: userform.html?status=error&msg={$msg}");
                exit();
            }

            $newName = uniqid('img_', true) . '.' . $ext;
            $destRel = 'uploads/' . $newName;
            $dest = $uploadDir . DIRECTORY_SEPARATOR . $newName;

            // Ensure the tmp file is a valid uploaded file
            if (!is_uploaded_file($tmp)) {
                $msg = urlencode('Temporary upload missing or not valid');
                error_log('Upload error: tmp file not an uploaded file: ' . $tmp);
                header("Location: userform.html?status=error&msg={$msg}");
                exit();
            }

            if (!move_uploaded_file($tmp, $dest)) {
                $last = error_get_last();
                $reason = $last ? $last['message'] : 'unknown';
                $msg = urlencode('Failed to save uploaded image: ' . $reason);
                error_log('Upload error: move_uploaded_file failed. tmp=' . $tmp . ' dest=' . $dest . ' reason=' . $reason);
                header("Location: userform.html?status=error&msg={$msg}");
                exit();
            }

            $image_path = $destRel;
        } else {
            $msg = urlencode('Image upload error');
            header("Location: userform.html?status=error&msg={$msg}");
            exit();
        }
    }

    // Ensure `suggestions` table exists with the required schema
    $createSql = "CREATE TABLE IF NOT EXISTS `suggestions` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        spot_name VARCHAR(255),
        spot_state VARCHAR(100),
        spot_district VARCHAR(100),
        description TEXT,
        image_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    if (!$conn->query($createSql)) {
        $err = urlencode('Failed to ensure suggestions table: ' . $conn->error);
        header("Location: userform.html?status=error&msg={$err}");
        exit();
    }

    // Use prepared statement to insert into suggestions
    $stmt = $conn->prepare("INSERT INTO suggestions (spot_name, spot_state, spot_district, description, image_path) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        $msg = urlencode('DB prepare failed: ' . $conn->error);
        header("Location: userform.html?status=error&msg={$msg}");
        exit();
    }

    // If no image was uploaded, store NULL or empty string — we'll use empty string for simplicity
    $imgParam = $image_path !== null ? $image_path : '';
    $stmt->bind_param('sssss', $spot_name, $spot_state, $spot_district, $description, $imgParam);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: userform.html?status=success');
        exit();
    } else {
        $err = urlencode($stmt->error);
        $stmt->close();
        $conn->close();
        header("Location: userform.html?status=error&msg={$err}");
        exit();
    }
}

$conn->close();
?>