<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include database connection
require_once '../config/db.php';

// Check if the blog ID is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid blog ID.");
}

// Query for Admin Name
$admin_id = $_SESSION['admin_id'];
$query = "SELECT name FROM admins WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$admin_name = $row['name'];

$blog_id = intval($_GET['id']);
$title = $content = $author = $featured_image = $tags = $seo_title = $seo_description = $seo_keywords = $seo_slug = $canonical_url = $meta_robots = $og_title = $og_description = "";
$title_err = $content_err = $author_err = $image_err = "";

// Fetch existing blog details
$sql = "SELECT title, content, author, featured_image, tags FROM blogs WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $blog_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $title = $row['title'];
            $content = $row['content'];
            $author = $row['author'];
            $featured_image = $row['featured_image'];
            $tags = $row['tags'];
        } else {
            die("Blog not found.");
        }
    } else {
        die("Database error.");
    }
    mysqli_stmt_close($stmt);
}

// Fetch SEO details from seo_meta table
$sql = "SELECT seo_title, seo_description, seo_keywords, seo_slug, canonical_url, meta_robots, og_title, og_description FROM seo_meta WHERE post_id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $blog_id);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $seo_title = $row['seo_title'];
            $seo_description = $row['seo_description'];
            $seo_keywords = $row['seo_keywords'];
            $seo_slug = $row['seo_slug'];
            $canonical_url = $row['canonical_url'];
            $meta_robots = $row['meta_robots'];
            $og_title = $row['og_title'];
            $og_description = $row['og_description'];
        }
    }
    mysqli_stmt_close($stmt);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    $title = trim($_POST["title"] ?? "");
    if (empty($title)) {
        $title_err = "Please enter a title.";
    }

    // Validate content
    $content = trim($_POST["content"] ?? "");
    if (empty($content)) {
        $content_err = "Please enter the content.";
    }

    // SEO Fields
    $seo_title = trim($_POST["seo_title"] ?? "");
    $seo_description = trim($_POST["seo_description"] ?? "");
    $seo_keywords = trim($_POST["seo_keywords"] ?? "");
    $seo_slug = trim($_POST["seo_slug"] ?? "");
    $canonical_url = trim($_POST["canonical_url"] ?? "");
    $meta_robots = trim($_POST["meta_robots"] ?? "");
    $og_title = trim($_POST["og_title"] ?? "");
    $og_description = trim($_POST["og_description"] ?? "");

    // Additional Fields
    $tags = trim($_POST["tags"] ?? "");

    // Handle file upload for featured image
    if (!empty($_FILES['featured_image']['name'])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["featured_image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ["jpg", "jpeg", "png"];

        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = uniqid("img_", true) . "." . $image_ext;
            $target_file = "$target_dir$new_image_name";

            if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                $featured_image = $new_image_name;
            } else {
                $image_err = "Error uploading file.";
            }
        } else {
            $image_err = "Only JPG, JPEG, and PNG files are allowed.";
        }
    } else {
        // Retain the original featured image if no new image is uploaded
        $featured_image = trim($_POST["existing_featured_image"] ?? "");
    }

    // Check for errors before updating the database
    if (empty($title_err) && empty($content_err) && empty($author_err) && empty($image_err)) {
        // Update `blogs` table
        $sql = "UPDATE blogs SET title = ?, content = ?, author = ?, featured_image = ?, tags = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssi", $title, $content, $admin_name, $featured_image, $tags, $blog_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Check if SEO metadata exists
        $sql = "SELECT id FROM seo_meta WHERE post_id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $blog_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                // Update existing SEO metadata
                $sql = "UPDATE seo_meta 
                        SET seo_title = ?, seo_description = ?, seo_keywords = ?, seo_slug = ?, canonical_url = ?, meta_robots = ?, og_title = ?, og_description = ? 
                        WHERE post_id = ?";
            } else {
                // Insert new SEO metadata
                $sql = "INSERT INTO seo_meta (seo_title, seo_description, seo_keywords, seo_slug, canonical_url, meta_robots, og_title, og_description, post_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            }
            mysqli_stmt_close($stmt);
        }

        // Prepare statement for inserting/updating SEO metadata
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssssssi", $seo_title, $seo_description, $seo_keywords, $seo_slug, $canonical_url, $meta_robots, $og_title, $og_description, $blog_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Redirect to manage posts page
        header("location: ../admin/dashboard.php?view=managePosts");
        exit();
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="../config/tinymce/js/tinymce/tinymce.min.js"
    referrerpolicy="origin"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        }

        .card {
            border-radius: 10px;
            padding: 20px;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            height: auto;
        }

        .dark-mode {
            background-color: #121212;
            color: #f8f9fa;
        }

        .dark-mode .card {
            background: #1e1e1e;
            color: #ffffff;
        }

        .toggle-dark {
            position: fixed;
            top: 20px;
            right: 20px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card_blog">
                    <h2 class="text-center mb-4">Edit Blog Post</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $blog_id; ?>"
                        method="post" enctype="multipart/form-data">
                        <input type="hidden" name="existing_featured_image" value="<?php echo htmlspecialchars($featured_image); ?>">
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control"
                                value="<?php echo htmlspecialchars($title); ?>">
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Content</label>
                            <textarea name="content" id="content"
                                class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
                            <div class="invalid-feedback"><?php echo $content_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control"
                                value="<?php echo htmlspecialchars($admin_name); ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label>SEO Title</label>
                            <input type="text" name="seo_title" class="form-control"
                                value="<?php echo htmlspecialchars($seo_title); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>SEO Description</label>
                            <textarea name="seo_description" class="form-control"><?php echo htmlspecialchars($seo_description); ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>SEO Keywords (comma separated)</label>
                            <input type="text" name="seo_keywords" class="form-control"
                                value="<?php echo htmlspecialchars($seo_keywords); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>SEO Slug</label>
                            <input type="text" name="seo_slug" class="form-control"
                                value="<?php echo htmlspecialchars($seo_slug); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical_url" class="form-control"
                                value="<?php echo htmlspecialchars($canonical_url); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>Meta Robots</label>
                            <select name="meta_robots" class="form-control">
                                <option value="index, follow" <?php echo ($meta_robots == 'index, follow') ? 'selected' : ''; ?>>Index, Follow</option>
                                <option value="noindex, follow" <?php echo ($meta_robots == 'noindex, follow') ? 'selected' : ''; ?>>No Index, Follow</option>
                                <option value="index, nofollow" <?php echo ($meta_robots == 'index, nofollow') ? 'selected' : ''; ?>>Index, No Follow</option>
                                <option value="noindex, nofollow" <?php echo ($meta_robots == 'noindex, nofollow') ? 'selected' : ''; ?>>No Index, No Follow</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>OG Title</label>
                            <input type="text" name="og_title" class="form-control"
                                value="<?php echo htmlspecialchars($og_title); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label>OG Description</label>
                            <textarea name="og_description" class="form-control"><?php echo htmlspecialchars($og_description); ?></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Featured Image</label>
                            <input type="file" name="featured_image" class="form-control">
                            <?php if (!empty($featured_image)): ?>
                                <?php
                                // Ensure correct path
                                $imagePath = "../uploads/" . basename($featured_image);
                                ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Featured Image" class="mt-2"
                                    style="max-width: 100px;">
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label>Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control"
                                value="<?php echo htmlspecialchars($tags); ?>">
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-warning">✏️ Update</button>
                            <a href="../admin/dashboard.php?view=managePosts" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview hr anchor pagebreak code paste',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | preview code',
            paste_data_images: true,
            images_upload_url: 'upload.php',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;

                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', 'upload.php');

                xhr.onload = function () {
                    if (xhr.status !== 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }

                    var json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location !== 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    success(json.location);
                };

                formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.send(formData);
            },
            height: 300
        });
    </script>
</body>

</html>