<?php
include '../config/db.php';  // DB Connection Include

// Fetch top trending blogs
$blogQuery = "SELECT title, views FROM blogs ORDER BY views DESC LIMIT 5";
$blogResult = mysqli_query($conn, $blogQuery);
$blogs = mysqli_fetch_all($blogResult, MYSQLI_ASSOC);

// Fetch top contributors
$userQuery = "SELECT author, COUNT(*) as posts FROM blogs GROUP BY author ORDER BY posts DESC LIMIT 5";
$userResult = mysqli_query($conn, $userQuery);
if ($userResult) {
    $users = mysqli_fetch_all($userResult, MYSQLI_ASSOC);
} else {
    $users = [];
}
?>