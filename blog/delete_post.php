<?php
session_start();
require_once '../config/db.php';

// Debugging: ID print karne ke liye
if (isset($_GET["id"])) {
    echo "Post ID received: " . $_GET["id"];
} else {
    echo "No ID received!";
}

// Check if post ID is provided and valid
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET["id"]); // Ensure ID is integer

    // Prepare SQL statement
    $sql = "DELETE FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: ../admin/dashboard.php?view=managePosts");
            exit();
        } else {
            echo "Error deleting post: {$stmt->error}";
        }
    } else {
        echo "Error in preparing statement: {$conn->error}";
    }
} else {
    echo "Invalid post ID!";
}
