<?php
session_start();
require_once '../config/db.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.tiny.cloud/1/your-tinymce-api-key/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <style>
        /* Your existing CSS styles */
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <h2 class="text-center mb-4">Create Blog Post</h2>
                    <form id="postForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <!-- Existing form fields -->
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <!-- Content editor -->
                        <div class="form-group mb-3">
                            <label>Content</label>
                            <textarea name="content" id="content" class="form-control" rows="10"></textarea>
                        </div>

                        <!-- AI Suggestions Button -->
                        <div class="form-group mb-3">
                            <button type="button" id="generateSeo" class="btn btn-primary mb-3">
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                ✨ Generate SEO Suggestions
                            </button>
                        </div>

                        <!-- SEO Fields -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>SEO Title</label>
                                <input type="text" name="seo_title" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>SEO Slug</label>
                                <input type="text" name="seo_slug" class="form-control">
                            </div>
                            <div class="col-12">
                                <label>SEO Description</label>
                                <textarea name="seo_description" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>SEO Keywords</label>
                                <input type="text" name="seo_keywords" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>Meta Robots</label>
                                <select name="meta_robots" class="form-control">
                                    <?php /* Options */ ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>OG Title</label>
                                <input type="text" name="og_title" class="form-control">
                            </div>
                            <div class="col-12">
                                <label>OG Description</label>
                                <textarea name="og_description" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-success">🚀 Publish Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // TinyMCE Initialization
        tinymce.init({
            selector: '#content',
            plugins: 'advlist autolink lists link image charmap preview hr anchor pagebreak code',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | code',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });

        // SEO Generation Handler
        document.getElementById('generateSeo').addEventListener('click', async function () {
            const btn = this;
            const spinner = btn.querySelector('.spinner-border');
            btn.disabled = true;
            spinner.classList.remove('d-none');

            try {
                const response = await fetch('../api/ai-seo-suggestions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?>' // Add this header
                    },
                    body: JSON.stringify({
                        title: document.querySelector('[name="title"]').value,
                        content: tinymce.get('content').getContent().substring(0, 1000)
                    })
                });

                if (!response.ok) throw new Error('API Error');
                const data = await response.json();

                // Populate fields
                document.querySelector('[name="seo_title"]').value = data.seoTitle;
                document.querySelector('[name="seo_description"]').value = data.seoDescription;
                document.querySelector('[name="seo_keywords"]').value = data.seoKeywords;
                document.querySelector('[name="seo_slug"]').value = data.seoSlug;
                document.querySelector('[name="canonical_url"]').value = data.canonicalUrl;
                document.querySelector('[name="meta_robots"]').value = data.metaRobots;
                document.querySelector('[name="og_title"]').value = data.ogTitle;
                document.querySelector('[name="og_description"]').value = data.ogDescription;

            } catch (error) {
                alert('Error generating suggestions: ' + error.message);
            } finally {
                btn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    </script>
</body>

</html>