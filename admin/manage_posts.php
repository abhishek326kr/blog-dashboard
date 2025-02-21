<?php
// Include database connection
include('../config/db.php');

// Fetch all posts from the database
$query = "SELECT * FROM blogs";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
            max-width: 80%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header_card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .btn {
            padding: 5px 12px;
            font-size: 14px;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .post-title-link {
            font-weight: bold;
            color: #17423C;
            text-decoration: none;
            transition: color 0.3s, text-decoration 0.3s;
        }

        .post-title-link:hover {
            color:rgb(1, 87, 75);
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header_card">
            <h2><i class="fas fa-newspaper"></i> Manage Posts</h2>
            <a href="../blog/create_post.php" class="btn btn-success"> <i class="fas fa-plus"></i> Create New Post</a>
        </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <a href="dashboard.php?view=post&id=<?php echo $row['id']; ?>" class="post-title-link">
                                <?php echo $row['title']; ?>
                            </a>
                        </td>
                    
                        <td><?php echo $row['author']; ?></td>
                        <td>
                            <a href="../blog/edit_post.php?id=<?php echo $row['id']; ?>" class="btn btn-warning"><i
                                    class="fas fa-edit"></i> Edit</a>
                            <a href="../blog/delete_post.php?id=<?php echo $row['id']; ?>" class="btn btn-danger"
                                onclick="return confirm('Are you sure you want to delete this post?');"><i
                                    class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>