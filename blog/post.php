<?php
// Database connection
include '../config/db.php';
include '../api/post.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($post['seo_description']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($post['seo_keywords']); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($post['author']); ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f9;
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
        <p><small class="text-muted">Published on: <?php echo date("M d, Y", strtotime($post['created_at'])); ?></small></p>
        <p><strong>Views:</strong> <?php echo htmlspecialchars($post['views']); ?></p>
        <hr>
        <p class="post-content"><?php echo nl2br($post['content']); ?></p>

        <a href="../admin/dashboard.php?view=managePosts" class="btn btn-primary mt-3">Back to Blogs</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
