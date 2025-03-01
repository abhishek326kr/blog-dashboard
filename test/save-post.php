<?php
session_start();
require_once '../config/db.php';

// Validate session and CSRF token
if (!isset($_SESSION['admin_id']) || 
    !isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    exit('Invalid request');
}

// Initialize variables and sanitize inputs
$title = htmlspecialchars(trim($_POST['title'] ?? ''));
$content = trim($_POST['content'] ?? '');
$seo_title = htmlspecialchars(trim($_POST['seo_title'] ?? ''));
$seo_slug = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($_POST['seo_slug'] ?? '')));

// Validate required fields
$errors = [];
if (empty($title)) $errors[] = 'Title is required';
if (empty($content)) $errors[] = 'Content is required';

// Handle file upload
$featured_image = '';
if ($_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['featured_image']['tmp_name']);
    
    if (!in_array($mime_type, $allowed_types)) {
        $errors[] = 'Invalid file type. Only JPEG/PNG allowed.';
    } else {
        $ext = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
        $filename = 'post-' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['featured_image']['tmp_name'], "../uploads/$filename");
        $featured_image = $filename;
    }
} else {
    $errors[] = 'Featured image is required';
}

// Database operations
if (empty($errors)) {
    try {
        mysqli_begin_transaction($conn);

        // Insert main post
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO posts (title, content, featured_image) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'sss', $title, $content, $featured_image);
        mysqli_stmt_execute($stmt);
        $post_id = mysqli_insert_id($conn);

        // Insert SEO data
        $seo_stmt = mysqli_prepare($conn,
            "INSERT INTO seo_meta (post_id, seo_title, seo_description, seo_keywords, seo_slug, canonical_url, meta_robots, og_title, og_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($seo_stmt, 'issssssss', 
            $post_id,
            $_POST['seo_title'],
            $_POST['seo_description'],
            $_POST['seo_keywords'],
            $seo_slug,
            $_POST['canonical_url'],
            $_POST['meta_robots'],
            $_POST['og_title'],
            $_POST['og_description']
        );
        mysqli_stmt_execute($seo_stmt);

        mysqli_commit($conn);
        header('Location: /admin/posts?success=1');
        exit();

    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        error_log("Database error: " . $e->getMessage());
        $errors[] = 'Database error occurred';
    }
}

// If errors occurred
$_SESSION['form_errors'] = $errors;
header('Location: create-post');
exit();
?>