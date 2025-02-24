<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow frontend to access API

// Database Connection
include '../config/db.php';

if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode(["error" => "Search query is missing"]);
    exit;
}

$searchTerm = "%" . $_GET['query'] . "%"; // Partial match for search

$sql = "SELECT id, title FROM blogs WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode(["results" => $blogs]);
?>
