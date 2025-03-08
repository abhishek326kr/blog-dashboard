<?php
$host = "localhost"; 
$username = "root";  // Hostinger pe "root" nahi hoga, waha jo hostinger ka diya hoga wo dalna
$password = "StrongP@ssw0rd!";
$database = "blog_dashboard";

// Database Connection
$conn = new mysqli($host, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
