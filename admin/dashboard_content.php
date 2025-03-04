<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
include '../config/db.php';
include '../analytics/analytics.php';
// Fetch SEO Data from the API
$apiUrl = '../api/fetch_seo_data.php';
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
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

        .dark-mode p {
            color: #f8f9fa !important;
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

        <div class="row mt-5">

            <div class="col-md-3 fade-in">
                <div class="card card_dashboad text-center p-3">
                    <div class="card-body">
                        <i data-feather="trending-up" class="icon-box"></i> <!-- Updated icon -->
                        <h5>Search Traffic</h5>
                        <h3><span id="search_traffic">Loading...</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.2s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="bar-chart-2" class="icon-box"></i> <!-- Updated icon -->
                        <h5>Search Impressions</h5>
                        <h3><span id="search_impressions">Loading...</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.4s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="list" class="icon-box"></i> <!-- Updated icon -->
                        <h5>Total Keywords</h5>
                        <h3><span id="total_keywords">Loading...</span></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 fade-in" style="animation-delay: 0.6s;">
                <div class="card text-center p-3">
                    <div class="card-body">
                        <i data-feather="target" class="icon-box"></i> <!-- Updated icon -->
                        <h5>Avg. Position</h5>
                        <h3><span id="avg_position">Loading...</span></h3>
                    </div>
                </div>
            </div>
            <p class="text-muted text-center mt-3" style="font-size: 14px;">
                📊 Data shown for the last <strong>28 days</strong>. For full analytics, check
                <a href="https://analytics.google.com/" target="_blank" style="text-decoration: none;">
                    Google Analytics
                </a>.
            </p>

        </div>





        <div class="recent-blogs fade-in" style="animation-delay: 0.8s;">
            <h3><i class="fas fa-book-open"></i> Recent Blogs</h3>
            <?php include '../blog/blog.php'; ?>
        </div>
    </div>

    <script>
        feather.replace();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function fetchSEOData(days) {
                fetch(`../api/fetch_seo_data.php?days=${days}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("search_traffic").innerText = data.search_traffic || "N/A";
                        document.getElementById("search_impressions").innerText = data.search_impressions || "N/A";
                        document.getElementById("total_keywords").innerText = data.total_keywords || "N/A";
                        document.getElementById("avg_position").innerText = data.avg_position || "N/A";
                    })
                    .catch(error => console.error("Fetch Error:", error));
            }

            // Default to 7 days on load
            fetchSEOData(28);

            // Event listener for date range selection
            document.getElementById("dateRangeSelect").addEventListener("change", function () {
                const days = this.value;
                fetchSEOData(days);
            });
        });

    </script>

</body>

</html>