<?php
include '../config/db.php';

if (!isset($_GET['query']) || empty(trim($_GET['query']))) {
    echo json_encode(["results" => [], "error" => "Invalid search query"]);
    exit;
}

$query = "%" . trim($_GET['query']) . "%";

if ($stmt = $conn->prepare("SELECT id, title FROM blogs WHERE title LIKE ? OR content LIKE ? LIMIT 10")) {
    $stmt->bind_param("ss", $query, $query);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(["results" => $searchResults]);
    } else {
        echo json_encode(["results" => [], "error" => "Query execution failed"]);
    }

    $stmt->close();
} else {
    echo json_encode(["results" => [], "error" => "Failed to prepare statement"]);
}

$conn->close();
?>
