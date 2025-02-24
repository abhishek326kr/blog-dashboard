<?php 
include '../config/db.php'; 
include '../analytics/analytics.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            font-family: 'Arial', sans-serif;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
        }

        

        .dark-mode {
            background-color: #121212;
            color: #f8f9fa;
        }

      

        .toggle-dark {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .card {
            border-radius: 20px !important;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease-in-out;
            background: white;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card-body h5 {
            font-weight: 600;
            color: #333;
        }
        .icon-box {
            font-size: 30px;
            color: #007bff;
            margin-bottom: 10px;
        }
        .recent-blogs {
            margin-top: 40px;
            
        }
        /* Hover Effect */
        .card:hover .icon-box {
            transform: rotateY(180deg);
            transition: 0.5s;
        }
        /* Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-in-out forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h2 class="text-center mb-4"><i class="fas fa-chart-bar"></i> Analytics Overview</h2>
        
        <div class="row">
            <div class="col-md-3 fade-in">
                <div class="card card_dashboad text-center p-3">
                    <div class="card-body">
                        <i data-feather="file-text" class="icon-box"></i>
                        <h5>Total Blogs</h5>
                        <h2><?php echo $totalBlogs; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.2s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="check-circle" class="icon-box"></i>
                        <h5>Published Blogs</h5>
                        <h3><?php echo $publishedBlogs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.4s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="edit-3" class="icon-box"></i>
                        <h5>Drafts</h5>
                        <h3><?php echo $draftBlogs; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.6s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="eye" class="icon-box"></i>
                        <h5>Views</h5>
                        <h3><?php echo $totalViews; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="recent-blogs fade-in" style="animation-delay: 0.8s;">
            <h3><i class="fas fa-book-open"></i> Recent Blogs</h3>
            <?php include '../blog/blog.php'; ?>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>
