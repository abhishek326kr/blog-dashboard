<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$user_id = $_SESSION['admin_id'];

// Fetch existing user data
$stmt = $conn->prepare("SELECT name, username, phone, profile_pic, password, email FROM admins WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(["error" => "User not found"]);
    exit();
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === "update_profile") {
        $name = htmlspecialchars(trim($_POST['name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);

        if (!$email) {
            echo json_encode(["error" => "Invalid email address"]);
            exit();
        }

        // Handle Profile Picture Upload
        $target_file = $user['profile_pic']; // Keep existing picture if no new one is uploaded
        if (!empty($_FILES['profile_pic']['name'])) {
            $target_dir = "../uploads/";
            $file_ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_ext, $allowed_ext)) {
                echo json_encode(["error" => "Invalid file format. Only JPG, PNG, and GIF allowed."]);
                exit();
            }

            $file_name = uniqid('profile_', true) . '.' . $file_ext;
            $target_file = "{$target_dir}{$file_name}";

            if (!move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                echo json_encode(["error" => "File upload failed!"]);
                exit();
            }

            // Delete old profile picture if it exists
            if ($user['profile_pic'] && file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']);
            }
        }

        // Update Query
        $stmt = $conn->prepare("UPDATE admins SET name = ?, username = ?, phone = ?, profile_pic = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $username, $phone, $target_file, $email, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Profile updated successfully!", "profile_pic" => $target_file]);
        } else {
            echo json_encode(["error" => "Error updating profile"]);
        }
        $stmt->close();
        exit();
    }

    // Handle Password Change
    if ($_POST['action'] === "change_password") {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Verify old password
        if (!password_verify($old_password, $user['password'])) {
            echo json_encode(["error" => "Incorrect old password"]);
            exit();
        }

        // Validate new password
        if (strlen($new_password) < 8) {
            echo json_encode(["error" => "New password must be at least 8 characters long"]);
            exit();
        }

        // Update to new password
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password_hash, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Password updated successfully!"]);
        } else {
            echo json_encode(["error" => "Error updating password"]);
        }
        $stmt->close();
        exit();
    }
}

// Fetch user data (if GET request)
echo json_encode([
    "name" => $user['name'],
    "username" => $user['username'],
    "phone" => $user['phone'],
    "profile_pic" => $user['profile_pic'],
    "email" => $user['email']
]);
exit();
?>