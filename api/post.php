
<?php
// Include database connection
include '../config/db.php';

// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "Invalid blog post ID"]);
    exit;
}

$id = intval($_GET['id']); // Sanitize input

// Fetch blog post with SEO data from seo_meta table
$sql = "SELECT 
            b.*, 
            sm.seo_title, 
            sm.seo_description, 
            sm.seo_keywords 
        FROM blogs b 
        LEFT JOIN seo_meta sm ON b.id = sm.post_id 
        WHERE b.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Blog post not found"]);
    exit;
}

$post = $result->fetch_assoc();
$current_views = intval($post['views']) + 1; // Increase views count

// Check if the request is from admin
if (!isset($_GET['admin']) || $_GET['admin'] != 'true') {
    // Update views count in database
    $update_sql = "UPDATE blogs SET views = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $current_views, $id);
    $update_stmt->execute();
    $update_stmt->close();
}

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
        "views" => $current_views, // Updated views count
        "seo_title" => isset($post['seo_title']) ? $post['seo_title'] : "",  // Default Empty String
        "seo_description" => isset($post['seo_description']) ? $post['seo_description'] : "",  // Default Empty String
        "seo_keywords" => isset($post['seo_keywords']) ? $post['seo_keywords'] : "",  // Default Empty String
        "featured_image" => !empty($post['featured_image']) ? "../uploads/" . $post['featured_image'] : null
    ]);
    exit;



?>