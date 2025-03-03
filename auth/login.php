<?php
session_start();
include '../config/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$error_message = ""; // Variable to store errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $query = "SELECT id, password FROM admins WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($admin_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin_id'] = $admin_id;
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Admin Login</title>

    <link rel="icon" href="../assets/images/favicon.ico" type="image/png">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root {
            --primary-color: #17423C;
            --secondary-color: #699D89;
            --background-color: #E9EFEC;
            --white: #fff;
            --gradient-start: #17423C;
            --gradient-end: #699D89;
        }

        body {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Public Sans', sans-serif;
            margin: 0;
        }

        .login-container {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h4 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .login-container p {
            color: var(--secondary-color);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .form-control {
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0px 0px 5px rgba(23, 66, 60, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
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
            transform: translateY(-2px);
        }

        .error-msg {
            margin-top: 10px;
            padding: 10px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border-radius: 5px;
            text-align: center;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 120px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>

    <form method="post" class="login-container animate__animated animate__fadeIn">
        <img src="../assets/images/flexy_dark_logo.png" alt="Logo" class="logo">

        <h4>Welcome Back! 👋</h4>
        <p>Please log in to your Admin account</p>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger error-msg"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" placeholder="Enter your Admin Email" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-login">Login</button>
    </form>

</body>
</html>