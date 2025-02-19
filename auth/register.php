<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);  // Secure hashing

    // Check if user already exists
    $sql_check = "SELECT id FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "Email already registered!";
    } else {
        // Insert User
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'viewer')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<form method="post">
    <input type="text" name="username" required placeholder="Enter Username"><br>
    <input type="email" name="email" required placeholder="Enter Email"><br>
    <input type="password" name="password" required placeholder="Enter Password"><br>
    <button type="submit">Register</button>
</form>
