<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
include '../config/db.php';

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle comment deletion
if (isset($_GET['delete'])) {
    $comment_id = intval($_GET['delete']);
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $comment_id);
    if ($stmt->execute()) {
        $success_message = "Comment deleted successfully.";
    } else {
        $error_message = "Error deleting comment.";
    }
    $stmt->close();
}

// Handle comment editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_comment'])) {
    $comment_id = intval($_POST['comment_id']);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);

    if (!empty($name) && !empty($email) && !empty($comment)) {
        $sql = "UPDATE comments SET name = ?, email = ?, comment = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $comment, $comment_id);
        if ($stmt->execute()) {
            $success_message = "Comment updated successfully.";
        } else {
            $error_message = "Error updating comment.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Fetch all comments
$sql = "SELECT c.*, b.title AS post_title 
        FROM comments c 
        LEFT JOIN blogs b ON c.post_id = b.id 
        ORDER BY c.created_at DESC";
$result = mysqli_query($conn, $sql);
$comments = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-action {
            margin: 2px;
            transition: transform 0.2s ease;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        .modal-content {
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .alert {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Manage Comments</h1>

        <!-- Display success/error messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Comments Table -->
        <div class="card p-4">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>

                        <th>Post Title</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Comment</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td>
                                <a href="../admin/dashboard.php?view=post&id=<?php echo htmlspecialchars($comment['post_id']); ?>"
                                    target="_blank">
                                    <?php echo htmlspecialchars($comment['post_title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($comment['name']); ?></td>
                            <td><?php echo htmlspecialchars($comment['email']); ?></td>
                            <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                            <td><?php echo htmlspecialchars($comment['created_at']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm btn-action" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?php echo $comment['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <!-- Delete Button -->
                                <a href="manage_comments.php?delete=<?php echo $comment['id']; ?>"
                                    class="btn btn-danger btn-sm btn-action"
                                    onclick="return confirm('Are you sure you want to delete this comment?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $comment['id']; ?>" tabindex="-1"
                            aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Comment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="manage_comments.php" method="post" class="edit-comment-form">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="<?php echo htmlspecialchars($comment['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="<?php echo htmlspecialchars($comment['email']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="comment" class="form-label">Comment</label>
                                                <textarea name="comment" class="form-control" rows="4"
                                                    required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
                                            </div>
                                            <button type="submit" name="edit_comment" class="btn btn-primary">Save
                                                Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>