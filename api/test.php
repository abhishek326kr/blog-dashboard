<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'vendor/autoload.php';

use Google\Client;
use Google\Service\Indexing;

// Ensure CSRF token is set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$config = [
    'google_credentials' => '../config/flexy-markets-dashboard-d97786040122.json',
    'indexnow_endpoint' => 'https://api.indexnow.org/indexnow',
    'indexnow_host' => 'flexymarkets.com',
    'indexnow_key' => '897169635ae248e6b4b59f0e306f6b3f',
    'log_file' => '../logs/index_logs.json'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new RuntimeException('Invalid CSRF token.');
        }

        // Validate and sanitize URL
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }

        // Initialize Google Client
        $client = new Client();
        $client->setAuthConfig($config['google_credentials']);
        $client->addScope('https://www.googleapis.com/auth/indexing');
        $indexingService = new Indexing($client);

        // Submit to Google Indexing API
        $googleRequest = new Indexing\UrlNotification();
        $googleRequest->setType('URL_UPDATED');
        $googleRequest->setUrl($url);
        $googleResponse = $indexingService->urlNotifications->publish($googleRequest);

        // Submit to IndexNow API
        $ch = curl_init($config['indexnow_endpoint']);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'host' => $config['indexnow_host'],
                'key' => $config['indexnow_key'],
                'urlList' => [$url]
            ])
        ]);

        $indexNowResponse = curl_exec($ch);
        if ($indexNowResponse === false) {
            throw new RuntimeException('IndexNow API request failed: ' . curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new RuntimeException("IndexNow API request failed with HTTP code $httpCode: $indexNowResponse");
        }

        // Log submission
        $logEntry = [
            'url' => $url,
            'timestamp' => date('c'),
            'google_status' => 'Success',
            'indexnow_status' => json_decode($indexNowResponse, true) ?: $indexNowResponse
        ];

        $logs = file_exists($config['log_file']) ? json_decode(file_get_contents($config['log_file']), true) : [];
        if (!is_array($logs)) {
            $logs = [];
        }
        array_unshift($logs, $logEntry);
        file_put_contents($config['log_file'], json_encode($logs, JSON_PRETTY_PRINT));

        echo json_encode([
            'success' => true,
            'message' => 'URL submitted successfully!',
            'log' => $logEntry,
            'details' => [
                'google' => 'Submitted to Google Indexing API successfully.',
                'indexnow' => 'Submitted to IndexNow API successfully.'
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'details' => [
                'google' => 'Failed to submit to Google Indexing API.',
                'indexnow' => 'Failed to submit to IndexNow API.'
            ]
        ]);
    }
    exit;
}

$logs = file_exists($config['log_file']) ? json_decode(file_get_contents($config['log_file']), true) : [];
if (!is_array($logs)) {
    $logs = [];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Indexing Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg,rgb(255, 255, 255) 0%,rgb(50, 97, 70) 100%);
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.98);
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }

        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Notification Toast -->
        <div id="notificationToast" class="toast notification-toast" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto"><i class="fas fa-bell me-2"></i>Submission Status</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body"></div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card mb-4 p-4">
                    <h2 class="mb-4 text-center text-purple">
                        <i class="fas fa-rocket me-2"></i>SEO Indexing Manager
                    </h2>
                    <form id="submitForm">
                        <div class="input-group mb-3">
                            <input type="url" name="url" class="form-control form-control-lg border-2"
                                placeholder="Enter URL to index (e.g., https://example.com)" required>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <span class="submit-text">Submit</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card p-4">
                    <h4 class="mb-3"><i class="fas fa-history me-2"></i>Submission History</h4>
                    <table class="table table-hover" id="logsTable">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-link me-1"></i>URL</th>
                                <th><i class="fas fa-clock me-1"></i>Timestamp</th>
                                <th><i class="fab fa-google me-1"></i>Google</th>
                                <th><i class="fas fa-bolt me-1"></i>IndexNow</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td class="text-truncate" style="max-width: 300px;"><?= htmlspecialchars($log['url']) ?>
                                    </td>
                                    <td><?= date('M j, Y H:i', strtotime($log['timestamp'])) ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success">Success</span></td>
                                    <td>
                                        <?php if (is_array($log['indexnow_status'])): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <?= htmlspecialchars($log['indexnow_status']['message'] ?? 'Success') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <?= htmlspecialchars($log['indexnow_status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#logsTable').DataTable({
                order: [[1, 'desc']],
                columnDefs: [
                    { targets: 0, width: '40%' },
                    { targets: 1, width: '20%' },
                    { targets: [2, 3], width: '15%' }
                ]
            });
        });

        $('#submitForm').submit(async function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const $spinner = $btn.find('.spinner-border');
            const $submitText = $btn.find('.submit-text');

            $spinner.removeClass('d-none');
            $submitText.text('Submitting...');
            $btn.prop('disabled', true);

            try {
                const response = await fetch('', { method: 'POST', body: new FormData(this) });
                const text = await response.text(); // Get raw response text first

                let data;
                try {
                    data = JSON.parse(text); // Attempt to parse JSON
                } catch (error) {
                    console.error('Failed to parse JSON:', text);
                    throw new Error('Invalid server response. Please check the server logs.');
                }

                const toast = new bootstrap.Toast(document.getElementById('notificationToast'));
                const toastBody = $('#notificationToast .toast-body');

                if (data.success) {
                    toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <div>
                        <strong>Success!</strong><br>
                        ${data.message}<br>
                        <small>Google: ${data.details.google}</small><br>
                        <small>IndexNow: ${data.details.indexnow}</small>
                    </div>
                </div>
            `);
                    $('#notificationToast').removeClass('text-bg-danger').addClass('text-bg-success');
                } else {
                    toastBody.html(`
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle text-danger me-2"></i>
                    <div>
                        <strong>Error!</strong><br>
                        ${data.message}<br>
                        <small>Google: ${data.details.google}</small><br>
                        <small>IndexNow: ${data.details.indexnow}</small>
                    </div>
                </div>
            `);
                    $('#notificationToast').removeClass('text-bg-success').addClass('text-bg-danger');
                }

                toast.show();
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please check the console for details.');
            } finally {
                $spinner.addClass('d-none');
                $submitText.text('Submit');
                $btn.prop('disabled', false);
            }
        });
    </script>
</body>

</html>