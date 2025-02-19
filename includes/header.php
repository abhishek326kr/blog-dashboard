<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogging Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Custom CSS -->
</head>
<body>
    <header class="navbar navbar-dark bg-primary p-3">
        <div class="container-fluid">
            <a href="dashboard.php" class="navbar-brand">Blogging Dashboard</a>
            <div class="d-flex">
                <span class="text-white me-3">Welcome, <?php echo $_SESSION['username'] ?? 'Guest'; ?></span>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </header>
