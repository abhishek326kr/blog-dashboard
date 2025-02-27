<?php
session_start();

// Configuration
$google_api_key = '../config/flexy-markets-dashboard-bf9ca1f051ba.json';
$indexnow_api_key = 'YOUR_INDEXNOW_API_KEY';
$log_file = 'index_logs.json';

// Initialize log file if not exists
if (!file_exists($log_file)) {
    file_put_contents($log_file, json_encode([]));
}

// Function to log indexing attempts
function logIndexAttempt($url, $service, $status) {
    global $log_file;
    $log = json_decode(file_get_contents($log_file), true);
    $log[] = [
        'url' => $url,
        'service' => $service,
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents($log_file, json_encode($log));
}

// Rest of your existing PHP functions here (sendToGoogleIndexing, sendToIndexNow)...

function sendToGoogleIndexing($url) {
    global $google_api_key;
    
    $endpoint = "https://indexing.googleapis.com/v3/urlNotifications:publish?key={$google_api_key}";
    $postData = json_encode(["url" => $url, "type" => "URL_UPDATED"]);

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        return "✅ Google Indexing Successful for $url";
    } elseif ($httpCode == 403) {
        return "❌ Error 403: Permission denied. Verify URL ownership.";
    } else {
        return "❌ Google Indexing Failed ($httpCode): " . json_decode($response, true)['error']['message'];
    }
}


// Modified form handling to include logging
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = trim($_POST['url']);
    $action = $_POST['action'];
    
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        try {
            if ($action == 'google') {
                $result = sendToGoogleIndexing($url);
                logIndexAttempt($url, 'Google', stripos($result, 'Successful') !== false ? 'success' : 'error');
            } elseif ($action == 'indexnow') {
                $result = sendToIndexNow($url);
                logIndexAttempt($url, 'IndexNow', stripos($result, 'Successful') !== false ? 'success' : 'error');
            } else {
                throw new Exception("❌ Invalid Action Selected!");
            }
            $_SESSION['result'] = $result;
        } catch (Exception $e) {
            $_SESSION['result'] = $e->getMessage();
            logIndexAttempt($url, $action, 'error');
        }
    } else {
        $_SESSION['result'] = "❌ Invalid URL!";
    }
    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}

// Get indexing history
$index_logs = json_decode(file_get_contents($log_file), true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instant Indexing Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        body {
            background: var(--secondary-gradient);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            transition: transform 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.9rem;
        }

        .status-success {
            background: #d1fae5;
            color: #065f46;
        }

        .status-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .animated-alert {
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .service-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: white;
        }
    </style>
</head>
<body class="py-5">
    <div class="container">
        <div class="glass-card p-4 mb-5">
            <h1 class="text-center mb-4 display-5 fw-bold">
                <i class="fas fa-rocket me-2"></i>Instant Indexing Dashboard
            </h1>
            
            <?php if(isset($_SESSION['result'])): ?>
            <div class="animated-alert alert alert-dismissible fade show <?php 
                echo strpos($_SESSION['result'], '❌') !== false ? 'alert-danger' : 'alert-success'; ?>">
                <?= $_SESSION['result'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['result']); endif; ?>

            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="url" name="url" class="form-control form-control-lg" 
                           placeholder="https://example.com" required>
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-lg">
                        <option value="google">Google</option>
                        <option value="indexnow">IndexNow</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-paper-plane me-2"></i>Submit
                    </button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h3 class="mb-4"><i class="fas fa-history me-2"></i>Indexing History</h3>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_reverse($index_logs) as $log): ?>
                                <tr>
                                    <td>
                                        <div class="service-icon">
                                            <?= $log['service'] === 'Google' ? 'G' : 'I' ?>
                                        </div>
                                    </td>
                                    <td class="text-truncate" style="max-width: 200px;">
                                        <?= htmlspecialchars($log['url']) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $log['status'] === 'success' ? 
                                            'status-success' : 'status-error' ?>">
                                            <?= ucfirst($log['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('H:i', strtotime($log['timestamp'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="glass-card p-4 h-100">
                    <h3 class="mb-4"><i class="fas fa-chart-bar me-2"></i>Statistics</h3>
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <div class="display-4 fw-bold text-primary">
                                <?= count(array_filter($index_logs, fn($log) => $log['status'] === 'success')) ?>
                            </div>
                            <div class="text-muted">Successful Indexes</div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="display-4 fw-bold text-danger">
                                <?= count(array_filter($index_logs, fn($log) => $log['status'] === 'error')) ?>
                            </div>
                            <div class="text-muted">Failed Attempts</div>
                        </div>
                    </div>
                    <canvas id="statsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize chart
        const ctx = document.getElementById('statsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Google', 'IndexNow'],
                datasets: [{
                    data: [
                        <?= count(array_filter($index_logs, fn($log) => $log['service'] === 'Google')) ?>,
                        <?= count(array_filter($index_logs, fn($log) => $log['service'] === 'IndexNow')) ?>
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>