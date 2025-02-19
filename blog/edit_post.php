<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category = trim($_POST["category"]);
    $status = $_POST["status"];

    // Check if new image is uploaded
    if ($_FILES["image"]["name"]) {
        $image = $_FILES["image"];
        $imageName = time() . "_" . basename($image["name"]);
        $imagePath = "uploads/" . $imageName;
        move_uploaded_file($image["tmp_name"], $imagePath);

        $sql = "UPDATE posts SET title=?, content=?, category=?, status=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisi", $title, $content, $category, $status, $imageName, $id);
    } else {
        $sql = "UPDATE posts SET title=?, content=?, category=?, status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $title, $content, $category, $status, $id);
    }

    if ($stmt->execute()) {
        echo "Post updated successfully! <a href='manage_posts.php'>Back to Posts</a>";
    } else {
        echo "Error: " . $conn->error;
    }
}

?>

<form method="post">
    <input type="text" name="title" value="<?php echo $post['title']; ?>" required><br>
    <textarea name="content"><?php echo $post['content']; ?></textarea><br>
    <input type="text" name="category" value="<?php echo $post['category']; ?>"><br>
    <select name="status">
        <option value="draft" <?php echo ($post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
        <option value="published" <?php echo ($post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
    </select><br>
    <button type="submit">Update Post</button>
</form>
