<?php
// Include database connection
include '../config/db.php';

session_start();
$user_id = $_SESSION['admin_id']; // Ensure this session variable exists

// Fetch user details
$query = "SELECT * FROM admins WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];

    // Update Query
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE admins SET name='$name', email='$email', username='$username', phone='$phone', password='$hashed_password' WHERE id='$user_id'";
    } else {
        $update_query = "UPDATE admins SET name='$name', email='$email', username='$username', phone='$phone' WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = window.location.href;</script>";
        exit();
    } else {
        echo "<script>alert('Error updating profile: " . mysqli_error($conn) . "');</script>";
    }
}
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
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }

        .dark-mode {
            background-color:rgb(54, 54, 54);
            color: #f8f9fa;
        }

        .dark-mode a {
            color: #f8f9fa;
        }

        .dark-mode .profile-container {
            background-color: #222;
            color: #f8f9fa;
        }

        .dark-mode .table>:not(caption)>*>* {
            border-color: #444;
            background: #222;
            color: white;
        }

        .toggle-dark {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .profile-container {
            background: white;
            max-width: 450px;
            margin: 50px auto;
            border: 1px solid #ddd;
            padding: 25px;
            border-radius: 15px;
  
            box-shadow: 0 6px 15px rgba(255, 255, 255, 0.1);
            transition: 0.3s ease-in-out;
        }

        .profile-container:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
        }

        .profile-pic {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            margin-bottom: 15px;
            transition: 0.3s ease-in-out;
        }

        .profile-pic:hover {
            transform: scale(1.1);
        }

        .edit-form {
            display: none;
        }

        .form-control {
            background: #333;
            border: none;
            color: white;
        }

        .form-control:focus {
            background: #444;
            color: white;
            box-shadow: none;
        }

        .btn-custom {
            background: #ff5e00;
            color: white;
            transition: 0.3s ease-in-out;
        }

        .btn-custom:hover {
            background: #ff751a;
        }

       
    </style>
</head>

<body>

    <div class="profile-container">
        <img src="https://files.mastodonapp.uk/cache/accounts/avatars/109/896/082/068/480/122/original/1ff7a65dec79f6ea.jpg"
            class="profile-pic" alt="Profile Picture">

        <div class="profile-details">
            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
            <p><i class="fas fa-user"></i> <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?>
            </p>
            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
            </p>
            <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><i class="fas fa-user-shield"></i> <strong>Role:</strong> Admin</p>
            <p><i class="fas fa-calendar-alt"></i> <strong>Joined:</strong>
                <?php echo date("F d, Y", strtotime($user['created_at'])); ?></p>
            <button class="btn btn-custom btn-sm" id="editProfileBtn">Edit Profile</button>
        </div>

        <!-- Edit Form -->
        <form method="POST" class="edit-form" id="editForm">
            <div class="mb-2">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                    value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                    value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="mb-2">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">New Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
        </form>

    </div>

    

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let editBtn = document.getElementById("editProfileBtn");
            let profileDetails = document.querySelector(".profile-details");
            let editForm = document.getElementById("editForm");

            if (editBtn && profileDetails && editForm) {
                editBtn.addEventListener("click", function () {
                    profileDetails.style.display = "none";
                    editForm.style.display = "block";
                });

                // Add Cancel Button for reverting back
                let cancelBtn = document.createElement("button");
                cancelBtn.innerText = "Cancel";
                cancelBtn.classList.add("btn", "btn-secondary", "btn-sm", "mt-2");
                cancelBtn.addEventListener("click", function () {
                    profileDetails.style.display = "block";
                    editForm.style.display = "none";
                });

                // Ensure cancel button is added only once
                if (!editForm.querySelector(".btn-secondary")) {
                    editForm.appendChild(cancelBtn);
                }
            } else {
                console.error("Element(s) not found! Check IDs and class names.");
            }
        });
    </script>

</body>

</html>