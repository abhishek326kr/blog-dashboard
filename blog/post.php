<?php
// Include database connection
include '../config/db.php';

// Set headers for JSON response
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

// Check if the request is for JSON response
if (isset($_GET['json']) && $_GET['json'] == 'true') {
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
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="title" content="<?php echo htmlspecialchars($post['seo_title']); ?>">
    <meta name="description"
        content="<?php echo htmlspecialchars(isset($post['seo_description']) ? $post['seo_description'] : ''); ?>">
    <meta name="keywords"
        content="<?php echo htmlspecialchars(isset($post['seo_keywords']) ? $post['seo_keywords'] : ''); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($post['seo_title']); ?>">
    <meta property="og:description"
        content="<?php echo htmlspecialchars(isset($post['seo_description']) ? $post['seo_description'] : ''); ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }


        .dark-mode {
            background-color: rgb(54, 54, 54);
            color: #f8f9fa;
        }

        .dark-mode a {
            color: #f8f9fa;
        }

        .dark-mode .container {
            background-color: #222;
            color: #f8f9fa;
        }

        .dark-mode .table>:not(caption)>*>* {
            border-color: #444;
            background: #222;
            color: white;
        }

        .toggle-dark {
            position: fixed;
            top: 20px;
            right: 20px;
        }


        .container {
            max-width: 1200px;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .featured-img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (!empty($post['featured_image'])): ?>
            <img src="<?php echo "../uploads/" . htmlspecialchars($post['featured_image']); ?>" alt="Featured Image"
                class="featured-img">
        <?php endif; ?>
        <h1 class="mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><strong>By:</strong> <?php echo htmlspecialchars($post['author']); ?></p>
        <p><small class="text-muted">Published on: <?php echo date("M d, Y", strtotime($post['created_at'])); ?></small>
        </p>
        <p><strong>Views:</strong> <?php echo htmlspecialchars($current_views); ?></p>
        <hr>
        <p class="post-content"><?php echo nl2br($post['content']); ?></p>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>