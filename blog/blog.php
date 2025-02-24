<?php
// Database connection
include '../config/db.php';

// Fetch blogs from database
$sql = "SELECT * FROM blogs";

$blog = $conn->query($sql);

$sn = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        

        .dark-mode {
            background-color:rgb(54, 54, 54);
            color: #f8f9fa;
        }

        .dark-mode a {
            color: #f8f9fa;
        }

        .dark-mode .table-container {
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
        .table-container {
            max-width: 100%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            overflow: hidden;
            border-radius: 10px;
        }

      
        thead {
            color: white;
        }
        tbody tr {
            transition: 0.3s;
        }
        tbody tr:hover {
            background: rgba(0, 123, 255, 0.1);
        }
        td {
            vertical-align: middle;
        }
        
        .post-title-link {
            font-weight: bold;
            color: #17423C;
            text-decoration: none;
            transition: color 0.3s, text-decoration 0.3s;
        }

        .post-title-link:hover {
            color:rgb(1, 87, 75);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="table-container">
     
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($blog->num_rows > 0) {
                    while($row = $blog->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $sn++ . "</td>"; // Serial number
                        echo "<td><a href='../admin/dashboard.php?view=post&id=" . $row['id'] . "' class='post-title-link'>" . $row['title'] . "</a></td>";
                        echo "<td>" . $row["author"] . "</td>";
                        echo "<td>" . date("M d, Y", strtotime($row["created_at"])) . "</td>";
                        echo "</tr>";
                    }

                    
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No results found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
