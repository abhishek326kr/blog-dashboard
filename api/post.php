<?php
// Include database connection
include '../config/db.php';

// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow access from any origin
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Invalid blog post ID"]);
    exit;
}

$id = intval($_GET['id']); // Sanitize input

// Fetch blog post from database
$sql = "SELECT * FROM blogs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Blog post not found"]);
    exit;
}

$post = $result->fetch_assoc();

// Close connection
$stmt->close();
$conn->close();

// Send JSON response
echo json_encode([
    "id" => $post['id'],
    "title" => $post['title'],
    "author" => $post['author'],
    "published_date" => $post['created_at'],
    "content" => $post['content'],
    "views" => $post['views'],
    "seo_description" => $post['seo_description'],
    "seo_keywords" => $post['seo_keywords'],
    "featured_image" => !empty($post['featured_image']) ? "../uploads/" . $post['featured_image'] : null
]);
