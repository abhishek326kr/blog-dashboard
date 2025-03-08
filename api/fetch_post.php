<?php
// Include database connection
include '../config/db.php';

// Set headers for JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Initialize variables
$post = null;
$current_views = 0;

// Check if slug is provided (priority to slug over ID)
if (isset($_GET['slug'])) {
    $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_STRING);
    if (empty($slug)) {
        echo json_encode(["error" => "Invalid blog post slug"]);
        exit;
    }

    $sql = "SELECT 
                b.*, 
                sm.seo_title, 
                sm.seo_description, 
                sm.seo_keywords,
                sm.seo_slug,
                sm.canonical_url,
                sm.meta_robots,
                sm.og_title,
                sm.og_description
            FROM blogs b 
            LEFT JOIN seo_meta sm ON b.id = sm.post_id 
            WHERE sm.seo_slug = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $slug);
} elseif (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (empty($id)) {
        echo json_encode(["error" => "Invalid blog post ID"]);
        exit;
    }

    $sql = "SELECT 
                b.*, 
                sm.seo_title, 
                sm.seo_description, 
                sm.seo_keywords,
                sm.seo_slug,
                sm.canonical_url,
                sm.meta_robots,
                sm.og_title,
                sm.og_description
            FROM blogs b 
            LEFT JOIN seo_meta sm ON b.id = sm.post_id 
            WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
} else {
    echo json_encode(["error" => "Please provide a blog post slug or ID"]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Blog post not found"]);
    exit;
}

$post = $result->fetch_assoc();
$current_views = intval($post['views']) + 1;

// Update views count (only for non-admin users)
if (!isset($_GET['admin']) || $_GET['admin'] != 'true') {
    $update_sql = "UPDATE blogs SET views = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ii", $current_views, $post['id']);
    $update_stmt->execute();
    $update_stmt->close();
}

// JSON Response
echo json_encode([
    "id" => $post['id'],
    "title" => $post['title'],
    "author" => $post['author'],
    "published_date" => $post['created_at'],
    "content" => $post['content'],
    "views" => $current_views,
    "seo_title" => $post['seo_title'] ?? $post['title'],
    "seo_description" => $post['seo_description'] ?? 'A blog post by ' . $post['author'],
    "seo_keywords" => $post['seo_keywords'] ?? 'blog, post, article',
    "seo_slug" => $post['seo_slug'] ?? $post['id'],
    "canonical_url" => "https://flexymarkets.com/blog/" . ($post['seo_slug'] ?? $post['id']),
    "meta_robots" => $post['meta_robots'] ?? 'index, follow',
    "og_title" => $post['og_title'] ?? $post['title'],
    "og_description" => $post['og_description'] ?? 'A blog post by ' . $post['author'],
    "featured_image" => !empty($post['featured_image']) ? "https://flexymarkets.com/uploads/" . $post['featured_image'] : null,
]);
exit;
?>
