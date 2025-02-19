<?php
session_start();
include 'config/db.php';

// Only logged-in users can access
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $slug = strtolower(str_replace(" ", "-", $title));
    $content = trim($_POST["content"]);
    $author_id = $_SESSION["user_id"];
    $category = trim($_POST["category"]);
    $status = $_POST["status"];
    $meta_title = trim($_POST["meta_title"]);
    $meta_description = trim($_POST["meta_description"]);
    $meta_keywords = trim($_POST["meta_keywords"]);

    // Image Upload Handling
    $image = $_FILES["image"];
    $imageName = time() . "_" . basename($image["name"]); // Unique filename
    $imagePath = "uploads/" . $imageName;
    
    if (move_uploaded_file($image["tmp_name"], $imagePath)) {
        $sql = "INSERT INTO posts (title, slug, content, author_id, category, status, image, meta_title, meta_description, meta_keywords) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissssss", $title, $slug, $content, $author_id, $category, $status, $imageName, $meta_title, $meta_description, $meta_keywords);

        if ($stmt->execute()) {
            echo "Post created successfully! <a href='dashboard.php'>Go to Dashboard</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Image upload failed!";
    }
}

// Send Email

if ($stmt->execute()) {
    // Email Notification
    $to = "admin@example.com"; // Admin Email
    $subject = "New Blog Posted: $title";
    $message = "<h3>A new blog post has been published!</h3>
                <p><strong>Title:</strong> $title</p>
                <p><strong>Category:</strong> $category</p>
                <p><strong>Status:</strong> $status</p>
                <p><a href='https://yourwebsite.com/blog.php?slug=$slug'>View Blog</a></p>";

    sendEmail($to, $subject, $message);

    echo "Post created successfully! <a href='dashboard.php'>Go to Dashboard</a>";
}


?>

<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" required placeholder="Enter Title"><br>
    <textarea name="content" required placeholder="Enter Content"></textarea><br>
    <input type="file" name="image" accept="image/*" required><br>
    <input type="text" name="category" required placeholder="Enter Category"><br>
    <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
    </select><br>
    <input type="text" name="meta_title" placeholder="SEO Meta Title"><br>
    <textarea name="meta_description" placeholder="SEO Meta Description"></textarea><br>
    <input type="text" name="meta_keywords" placeholder="SEO Meta Keywords"><br>
    <button type="submit">Create Post</button>
</form>

