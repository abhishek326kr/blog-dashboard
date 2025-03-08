<?php
require_once '../config/db.php'; // Database connection

$target_dir = "/var/www/html/blog-dashboard/uploads/"; // AWS EC2 ke hisaab se path
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $image_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
    $allowed_ext = ["jpg", "jpeg", "png"];

    if (!in_array($image_ext, $allowed_ext)) {
        echo json_encode(['error' => 'Invalid file type. Only JPG, JPEG, PNG allowed.']);
        exit;
    }

    $new_image_name = uniqid("img_", true) . "." . $image_ext;
    $target_file = $target_dir . $new_image_name;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Store in Database
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Database Connection Failed: " . $conn->connect_error);
        }

        $file_path = "/uploads/" . $new_image_name; // Relative path
        $stmt = $conn->prepare("INSERT INTO uploaded_files (file_name, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $new_image_name, $file_path);

        if ($stmt->execute()) {
            echo json_encode(['location' => $file_path]);
        } else {
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['error' => 'File upload failed.']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded.']);
}
?>
