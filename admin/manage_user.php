<?php
// Include database connection
include('../config/db.php');

// Fetch user details (Assume user ID is stored in session)
session_start();
$user_id = $_SESSION['admin_id']; // Change as needed

$query = "SELECT * FROM admins";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>

        body {
            background: #f4f7f9;
        }
       
        .profile-container {
            max-width: 100%;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);

            display: flex;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin-bottom: 15px;
        }

        .profile_details {
            margin-left: 20px;
            text-align: left;
        }
        
        .btn-edit {
            margin-top: 10px;
            background-color: white;
            color: #6a11cb;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="profile-container">
  <div class="pfp">
  <img src="https://files.mastodonapp.uk/cache/accounts/avatars/109/896/082/068/480/122/original/1ff7a65dec79f6ea.jpg" class="profile-pic" alt="Profile Picture">

  </div>    

  <div class="profile_details">
  <h2><?php echo htmlspecialchars($user['name']); ?></h2>
    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>

    <button class="btn btn-edit btn-sm">Edit Profile</button>
  </div>
    
</div>

</body>
</html>
