<?php
// Include database connection
require_once '../config/db.php';

// Define connection variable


// Initialize variables
$title = $content = $author = "";
$title_err = $content_err = $author_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = mysqli_real_escape_string($conn, trim($_POST["title"]));
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter the content.";
    } else {
        $content = mysqli_real_escape_string($conn, trim($_POST["content"]));
    }

    // Validate author
    if (empty(trim($_POST["author"]))) {
        $author_err = "Please enter the author name.";
    } else {
        $author = mysqli_real_escape_string($conn, trim($_POST["author"]));
    }

    // Check for errors before inserting into the database
    if (empty($title_err) && empty($content_err) && empty($author_err)) {
        $sql = "INSERT INTO blogs (title, content, author) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $title, $content, $author);

            if (mysqli_stmt_execute($stmt)) {
                header("location: ../admin/dashboard.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.tiny.cloud/1/xdzl24i0eyx673s1ukp65dwkobc1sj0foqjxgtj7fewqh0gc/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body { background-color: #f4f7f9; }
        .card { border-radius: 10px; padding: 20px; background: white; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .dark-mode { background-color: #121212; color: #f8f9fa; }
        .dark-mode .card { background: #1e1e1e; color: #ffffff; }
        .toggle-dark { position: fixed; top: 20px; right: 20px; }
    </style>
</head>
<body>
    <button class="btn btn-dark toggle-dark" onclick="toggleDarkMode()">🌙 Dark Mode</button>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h2 class="text-center mb-4">Create Blog Post</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Content</label>
                            <textarea name="content" id="content" class="form-control"><?php echo $content; ?></textarea>
                            <div class="invalid-feedback"><?php echo $content_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control <?php echo (!empty($author_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $author; ?>">
                            <div class="invalid-feedback"><?php echo $author_err; ?></div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success">🚀 Publish</button>
                            <a href="../admin/dashboard.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        tinymce.init({ selector: '#content', menubar: false, plugins: 'lists link image preview', toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview', height: 300 });
        function toggleDarkMode() { document.body.classList.toggle("dark-mode"); }
    </script>
</body>
</html>
