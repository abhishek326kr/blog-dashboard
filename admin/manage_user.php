<?php
// Include database connection
include '../config/db.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['admin_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
// $stmt->close(); // Removed unreachable code

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input sanitization
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = $_POST['password'];
    $target_file = $user['profile_pic'];

    // Handle File Upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../uploads/";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['profile_pic']['type'], $allowed_types) && 
            $_FILES['profile_pic']['size'] <= $max_size) {
            
            $file_name = uniqid('profile_') . '_' . basename($_FILES['profile_pic']['name']);
            $target_file = "{$target_dir}{$file_name}";

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                // Delete old profile picture if it exists
                if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                    unlink($user['profile_pic']);
                }
            } else {
                $_SESSION['error'] = "Failed to upload new profile picture";
                header("Location: profile.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid file type or size (max 2MB)";
            header("Location: profile.php");
            exit();
        }
    }

    // Prepare Update Query
    $update_fields = [
        'name' => $name,
        'username' => $username,
        'phone' => $phone,
        'profile_pic' => $target_file
    ];

    if (!empty($password)) {
        $update_fields['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($update_fields)));
    $stmt = $conn->prepare("UPDATE admins SET $set_clause WHERE id = ?");
    
    $types = str_repeat('s', count($update_fields)) . 'i';
    $values = array_values($update_fields);
    $values[] = $user_id;
    
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: admin/dashboard.php?view=profile");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile: {$stmt->error}";
        header("Location: admin/dashboard.php?view=profile");
        exit();
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #17423C;
            --secondary-color: #f8f9fa;
            --hover-color: #17423C;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
        }

        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .profile-sidebar {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.05);
        }

        .nav-links .nav-item {
            margin: 0.5rem 0;
        }

        .nav-links .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .nav-links .nav-link:hover {
            color: white;
            padding-left: 1rem;
        }

        .profile-content {
            background: white;
            padding: 2rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(74, 118, 168, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--hover-color);
            border-color: var(--hover-color);
        }

        .toggle-form-enter {
            animation: formSlide 0.3s ease-out;
        }

        @keyframes formSlide {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

    <div class="profile-container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-4 profile-sidebar">
                <div class="position-sticky" style="top: 2rem;">
                    <?php if (!empty($user['profile_pic'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_pic']) ?>" class="profile-pic" alt="Profile Picture">
                    <?php else: ?>
                        <div class="profile-pic bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>

                    <h4 class="mb-3"><?= htmlspecialchars($user['name']) ?></h4>
                    
                    <nav class="nav flex-column nav-links">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="showSection('profile')">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="showSection('security')">
                                <i class="fas fa-lock me-2"></i>Security
                            </a>
                        </li>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-8 profile-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Profile Section -->
                <div id="profileSection">
                    <h3 class="mb-4"><i class="fas fa-user-edit me-2"></i>Profile Information</h3>
                    <dl class="row">
                        <dt class="col-sm-3">Full Name</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user['name']) ?></dd>

                        <dt class="col-sm-3">Username</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user['username']) ?></dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user['email']) ?></dd>

                        <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9"><?= htmlspecialchars($user['phone']) ?: 'N/A' ?></dd>
                    </dl>
                    <button class="btn btn-primary" type="button" onclick="toggleEditForm()">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </button>
                </div>

                <!-- Edit Form -->
                <form method="POST" id="editForm" class="toggle-form-enter" style="display: none;" enctype="multipart/form-data">
                    <h3 class="mb-4"><i class="fas fa-user-cog me-2"></i>Edit Profile</h3>
                    
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="profile_pic" class="form-control" 
                               accept="image/jpeg, image/png, image/gif">
                        <small class="text-muted">Max size 2MB (JPEG, PNG, GIF)</small>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="toggleEditForm()">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>

                <!-- Security Section -->
                <div id="securitySection" style="display: none;">
                    <h3 class="mb-4"><i class="fas fa-shield-alt me-2"></i>Security Settings</h3>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter new password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" placeholder="Confirm new password">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleEditForm() {
            const profileSection = document.getElementById('profileSection');
            const editForm = document.getElementById('editForm');
            [profileSection, editForm].forEach(el => el.style.display = 
                el.style.display === 'none' ? 'block' : 'none');
        }

        function showSection(sectionId) {
            document.querySelectorAll('.profile-content > div').forEach(div => {
                div.style.display = 'none';
            });
            document.getElementById(sectionId + 'Section').style.display = 'block';
        }

        // Image Preview Handler
        document.querySelector('input[name="profile_pic"]').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-pic').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>