<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch all posts
$sql = "SELECT posts.id, posts.title, posts.status, users.username, posts.created_at 
        FROM posts 
        JOIN users ON posts.author_id = users.id 
        ORDER BY posts.created_at DESC";
$result = $conn->query($sql);
?>

<h2>Manage Blog Posts</h2>
<table border="1">
    <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Author</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><img src="uploads/<?php echo $row['image']; ?>" width="50"></td>
            <td><?php echo $row["title"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo ucfirst($row["status"]); ?></td>
            <td><?php echo $row["created_at"]; ?></td>
            <td>
                <a href="edit_post.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="delete_post.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>


    <?php } ?>
</table>
<a href="dashboard.php">Back to Dashboard</a>