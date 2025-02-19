<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Welcome to Admin Dashboard</h1>
    <ul>
    <li><a href="create_post.php">Create New Post</a></li>
    <li><a href="manage_posts.php">Manage Posts</a></li>
    <a href="../auth/logout.php">Logout</a>
</ul>
   
</body>
</html>
