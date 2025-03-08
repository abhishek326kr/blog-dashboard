<?php
// Headers to allow API access from anywhere
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Database connection
include '../config/db.php';

// Fetch blogs from database
$sql = "SELECT * FROM blogs ORDER BY created_at DESC";
$result = $conn->query($sql);

// Check if data is found
if ($result->num_rows > 0) {
    $blogs = array();
    while ($row = $result->fetch_assoc()) {
        $blogs[] = array(
            "id" => $row["id"],
            "title" => $row["title"],
            "author" => $row["author"],
            "created_at" => date("M d, Y", strtotime($row["created_at"]))
        );
    }
    
    // Return data as JSON
    echo json_encode(["status" => "success", "data" => $blogs], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["status" => "error", "message" => "No blogs found"]);
}

// Close connection
$conn->close();
?>
