<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if post ID is provided
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Delete post from database
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Post deleted successfully! <a href='manage_posts.php'>Back to Posts</a>";
    } else {
        echo "Error deleting post: " . $conn->error;
    }
} else {
    echo "Invalid post ID!";
}
?>
