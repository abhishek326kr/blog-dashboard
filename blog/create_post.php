<?php
// Include database connection
require_once '../config/db.php';

// Initialize variables
$title = $content = $author = $featured_image = "";
$seo_title = $seo_description = $seo_keywords = $seo_slug = $canonical_url = $meta_robots = "";
$og_title = $og_description = $og_image = "";
$title_err = $content_err = $author_err = $image_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    $title = trim($_POST["title"]);
    if (empty($title)) {
        $title_err = "Please enter a title.";
    }

    // Validate content
    $content = trim($_POST["content"]);
    if (empty($content)) {
        $content_err = "Please enter the content.";
    }

    // Validate author
    $author = trim($_POST["author"]);
    if (empty($author)) {
        $author_err = "Please enter the author name.";
    }

    // SEO Fields
    $seo_title = trim($_POST["seo_title"]);
    $seo_description = trim($_POST["seo_description"]);
    $seo_keywords = trim($_POST["seo_keywords"]);
    $seo_slug = trim($_POST["seo_slug"]);
    $canonical_url = trim($_POST["canonical_url"]);
    $meta_robots = trim($_POST["meta_robots"]);
    $og_title = trim($_POST["og_title"]);
    $og_description = trim($_POST["og_description"]);

    // Handle file upload for featured image
    if (!empty($_FILES["featured_image"]["name"])) {
        $target_dir = "../uploads/";
        $image_name = basename($_FILES["featured_image"]["name"]);
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_ext = ["jpg", "jpeg", "png"];

        if (in_array($image_ext, $allowed_ext)) {
            $new_image_name = uniqid("img_", true) . "." . $image_ext;
            $target_file = $target_dir . $new_image_name;

            if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
                $featured_image = $new_image_name;
            } else {
                $image_err = "Error uploading file.";
            }
        } else {
            $image_err = "Only JPG, JPEG, and PNG files are allowed.";
        }
    }

    // Check for errors before inserting into the database
    if (empty($title_err) && empty($content_err) && empty($author_err) && empty($image_err)) {
        // Insert into `blogs` table
        $sql = "INSERT INTO blogs (title, content, author, featured_image) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $title, $content, $author, $featured_image);

            if (mysqli_stmt_execute($stmt)) {
                $post_id = mysqli_insert_id($conn); // Get inserted post ID

                // Insert into `seo_meta` table
                $seo_sql = "INSERT INTO seo_meta (post_id, seo_title, seo_description, seo_keywords, seo_slug, canonical_url, meta_robots, og_title, og_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                if ($seo_stmt = mysqli_prepare($conn, $seo_sql)) {
                    mysqli_stmt_bind_param($seo_stmt, "issssssss", $post_id, $seo_title, $seo_description, $seo_keywords, $seo_slug, $canonical_url, $meta_robots, $og_title, $og_description);
                    mysqli_stmt_execute($seo_stmt);
                    mysqli_stmt_close($seo_stmt);
                }

                header("location: ../admin/dashboard.php?view=managePosts");
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
    <script src="https://cdn.tiny.cloud/1/xdzl24i0eyx673s1ukp65dwkobc1sj0foqjxgtj7fewqh0gc/tinymce/6/tinymce.min.js"
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
                    <h2 class="text-center mb-4">Create Blog Post</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                        enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title"
                                class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $title; ?>">
                            <div class="invalid-feedback"><?php echo $title_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Content</label>
                            <textarea name="content" id="content" class="form-control"
                                rows="10"><?php echo $content; ?></textarea>
                            <div class="invalid-feedback"><?php echo $content_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Author</label>
                            <input type="text" name="author"
                                class="form-control <?php echo (!empty($author_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php echo $author; ?>">
                            <div class="invalid-feedback"><?php echo $author_err; ?></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Featured Image</label>
                            <input type="file" name="featured_image"
                                class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                            <div class="invalid-feedback"><?php echo $image_err; ?></div>
                        </div>

                        <div class="form-group mb-3">
                            <label>SEO Title</label>
                            <input type="text" name="seo_title" class="form-control" value="<?php echo $seo_title; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label>SEO Description</label>
                            <textarea name="seo_description"
                                class="form-control"><?php echo $seo_description; ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label>SEO Keywords (comma separated)</label>
                            <input type="text" name="seo_keywords" class="form-control"
                                value="<?php echo $seo_keywords; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label>SEO Slug</label>
                            <input type="text" name="seo_slug" class="form-control" value="<?php echo $seo_slug; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label>Canonical URL</label>
                            <input type="text" name="canonical_url" class="form-control"
                                value="<?php echo $canonical_url; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label>Meta Robots</label>
                            <select name="meta_robots" class="form-control">
                                <option value="index, follow">Index, Follow</option>
                                <option value="noindex, follow">No Index, Follow</option>
                                <option value="index, nofollow">Index, No Follow</option>
                                <option value="noindex, nofollow">No Index, No Follow</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label>OG Title</label>
                            <input type="text" name="og_title" class="form-control" value="<?php echo $og_title; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label>OG Description</label>
                            <textarea name="og_description"
                                class="form-control"><?php echo $og_description; ?></textarea>
                        </div>



                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-success">🚀 Publish</button>
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