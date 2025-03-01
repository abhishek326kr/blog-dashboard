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

// Check if ID or slug is provided
if (isset($_GET['id'])){
    // Fetch blog post by ID
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
} elseif (isset($_GET['slug'])) {
    // Fetch blog post by slug
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
} else {
    // Neither ID nor slug provided
    echo json_encode(["error" => "Please provide a blog post ID or slug"]);
    exit;
}

// Execute the query
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
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
    $update_stmt->bind_param("ii", $current_views, $post['id']);
    $update_stmt->execute();
    $update_stmt->close();
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if (!empty($name) && !empty($email) && !empty($comment)) {
        $insert_sql = "INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("isss", $post['id'], $name, $email, $comment);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
}

// Fetch comments for this post
$comments_sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC";
$comments_stmt = $conn->prepare($comments_sql);
$comments_stmt->bind_param("i", $post['id']);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);

// Close connection
$stmt->close();
$comments_stmt->close();
$conn->close();

// Check if the request is for JSON response
if (isset($_GET['json']) && $_GET['json'] == 'true') {
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
        "canonical_url" => $canonical_url,
        "meta_robots" => $post['meta_robots'] ?? 'index, follow',
        "og_title" => $post['og_title'] ?? $post['title'],
        "og_description" => $post['og_description'] ?? 'A blog post by ' . $post['author'],
        "featured_image" => !empty($post['featured_image']) ? "../uploads/" . $post['featured_image'] : null,
        "comments" => $comments
    ]);
    exit;
}

// Generate canonical URL
$base_url = "https://flexymarkets.com/blog/";
$canonical_url = $base_url . htmlspecialchars($post['seo_slug'] ?? $post['id']);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="title" content="<?php echo htmlspecialchars($post['seo_title'] ?? $post['title']); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($post['seo_description'] ?? 'A blog post by ' . $post['author']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($post['seo_keywords'] ?? 'blog, post, article'); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($post['og_title'] ?? $post['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['og_description'] ?? 'A blog post by ' . $post['author']); ?>">
    <meta property="og:image" content="<?php echo !empty($post['featured_image']) ? "../uploads/" . htmlspecialchars($post['featured_image']) : ''; ?>">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $canonical_url; ?>">

    <!-- Robots Meta Tag -->
    <meta name="robots" content="<?php echo htmlspecialchars($post['meta_robots'] ?? 'index, follow'); ?>">

    <!-- Bootstrap CSS -->
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
            margin-bottom: 50px;
        }

        .container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .comment-section {
            margin-top: 40px;
        }

        .comment {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .comment:last-child {
            border-bottom: none;
        }

        .comment-author {
            font-weight: bold;
            color: #333;
        }

        .comment-date {
            font-size: 0.9em;
            color: #666;
        }

        .comment-form {
            margin-top: 20px;
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

        <!-- Comment Section -->
        <div class="comment-section">
            <h3>Comments</h3>
            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-author"><?php echo htmlspecialchars($comment['name']); ?></div>
                        <div class="comment-date"><?php echo date("M d, Y H:i", strtotime($comment['created_at'])); ?></div>
                        <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <!-- Comment Form -->
            <div class="comment-form">
                <h4>Leave a Comment</h4>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?slug=' . $slug; ?>" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea name="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>