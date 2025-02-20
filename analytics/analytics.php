<?php
include '../config/db.php';

// Total blogs count
$totalBlogsQuery = "SELECT COUNT(*) as total FROM blogs";
$totalBlogsResult = $conn->query($totalBlogsQuery);
$totalBlogs = $totalBlogsResult->fetch_assoc()['total'];

// Published blogs count
$publishedBlogsQuery = "SELECT COUNT(*) as total FROM blogs WHERE status='published'";
$publishedBlogsResult = $conn->query($publishedBlogsQuery);
$publishedBlogs = $publishedBlogsResult->fetch_assoc()['total'];

// Draft blogs count
$draftBlogsQuery = "SELECT COUNT(*) as total FROM blogs WHERE status='draft'";
$draftBlogsResult = $conn->query($draftBlogsQuery);
$draftBlogs = $draftBlogsResult->fetch_assoc()['total'];

// Total Views
$totalViewsQuery = "SELECT SUM(views) as total FROM blogs";
$totalViewsResult = $conn->query($totalViewsQuery);
$totalViews = $totalViewsResult->fetch_assoc()['total'];

// Recent Blogs
$recentBlogsQuery = "SELECT title, status, created_at as date FROM blogs ORDER BY created_at DESC LIMIT 5";
$recentBlogsResult = $conn->query($recentBlogsQuery);
$recentBlogs = [];
while ($row = $recentBlogsResult->fetch_assoc()) {
    $recentBlogs[] = $row;
}
?>
