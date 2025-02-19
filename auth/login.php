<?php
session_start();
include '../config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, password FROM admins WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    echo "<pre>";
    print_r($stmt);
    echo "</pre>";

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $hashed_password);
        $stmt->fetch();

        echo "Hashed Password from DB: " . $hashed_password . "<br>";
        echo "Entered Password: " . $password . "<br>";

        if (password_verify($password, $hashed_password)) {
            echo "Password Matched!";
            $_SESSION['admin_id'] = $admin_id;
            header("Location: ../admin/dashboard.php");
            exit();
        } else {
            echo "Password Not Matched!";
        }
    } else {
        echo "Admin Not Found!";
    }
}

?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Admin Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        /* 🌿 Theme Colors */
        :root {
            --primary-color: #17423C;
            --secondary-color: #699D89;
            --background-color: #E9EFEC;
            --white: #fff;
        }

        /* 🌟 Page Background */
        body {
            background-color: var(--background-color);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Public Sans', sans-serif;
        }

        /* 📌 Login Card */
        .login-container {
            background: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 380px;
      
        }

        /* 🔹 Header */
        .login-container h4 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .login-container p {
            color: var(--secondary-color);
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* 🔹 Input Fields */
        .form-control {
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0px 0px 5px rgba(23, 66, 60, 0.3);
        }

        /* 🔥 Submit Button */
        .btn-login {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            background: var(--primary-color);
            color: var(--white);
            transition: 0.3s ease-in-out;
        }

        .btn-login:hover {
            background: var(--secondary-color);
        }

        /* ❌ Error Message */
        .error-msg {
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <form method="post" class="login-container">
        <img src="../assets/images/flexy_dark_logo.png" alt="Logo" width="120">

        <h4>Welcome Back! 👋</h4>
        <p>Please log in to your Admin account</p>

        <?php if(isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Email</label>
            <input type="email" class="form-control"  name="email" placeholder="Enter your Admin Email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-login">Login</button>


    </form>

</body>
</html>


