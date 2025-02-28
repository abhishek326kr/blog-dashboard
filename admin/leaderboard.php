<?php include '../api/get_leaderboard.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <style>
        /* CSS Styling */

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

        .dark-mode .leaderboard-container {
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

        .dark-mode .leaderboard-trending-blogs, .dark-mode .leaderboard-top-contributors {
            width: 48%;
            background: #222;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .leaderboard-container {
     
            width: 100%;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            max-width: 1200px;
            margin: 50px auto;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .leaderboard-title {
            text-align: center;
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #006400,rgb(255, 255, 255));
            -webkit-background-clip: text;
            color: transparent;
        }

        .leaderboard-sections {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }

        .leaderboard-trending-blogs, .leaderboard-top-contributors {
            width: 48%;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .leaderboard-trending-blogs:hover, .leaderboard-top-contributors:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .leaderboard-section-title {
            color: #555;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            font-size: 1.5rem;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .leaderboard-list {
            list-style-type: none;
            padding: 0;
        }

        .leaderboard-list-item {
            background: #fff;
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .leaderboard-list-item:hover {
            transform: translateX(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .leaderboard-list-item span {
            font-weight: bold;
            color: #333;
        }

        .leaderboard-list-item span:first-child {
            color: #555;
        }

        .leaderboard-list-item span:last-child {
            color: #28a745;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .leaderboard-sections {
                flex-direction: column;
            }

            .leaderboard-trending-blogs, .leaderboard-top-contributors {
                width: 100%;
            }

            .leaderboard-title {
                font-size: 2rem;
            }

            .leaderboard-section-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="leaderboard-container">
        <h1 class="leaderboard-title">Leaderboard</h1>
        
        <div class="leaderboard-sections">
            <!-- Trending Blogs Section -->
            <div class="leaderboard-trending-blogs">
                <h2 class="leaderboard-section-title">Trending Blogs</h2>
                <ul class="leaderboard-list" id="trending-blogs-list">
                    <?php foreach ($blogs as $blog): ?>
                        <li class="leaderboard-list-item">
                            <span><?php echo htmlspecialchars($blog['title']); ?></span>
                            <span><?php echo $blog['views']; ?> views</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Top Contributors Section -->
            <div class="leaderboard-top-contributors">
                <h2 class="leaderboard-section-title">Top Contributors</h2>
                <ul class="leaderboard-list" id="top-contributors-list">
                    <?php foreach ($users as $user): ?>
                        <li class="leaderboard-list-item">
                            <span><?php echo htmlspecialchars($user['author']); ?></span>
                            <span><?php echo $user['posts']; ?> posts</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
