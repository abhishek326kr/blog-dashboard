<?php
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\Webmasters;

function getSearchConsoleData($days) {
    $client = new Client();
    $client->setAuthConfig(__DIR__ . '/credentials.json'); // Ensure correct path
    $client->setScopes(Webmasters::WEBMASTERS_READONLY);
    $client->setAccessType('offline'); 
    $client->setPrompt('select_account consent');

    // Manually authenticate and set the access token
    $tokenPath = __DIR__ . '/token.json';
    
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // Refresh the token if it's expired
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }

    if (!$client->getAccessToken()) {
        echo json_encode(['error' => 'Failed to get access token']);
        exit;
    }

    $service = new Webmasters($client);
    $siteUrl = 'sc-domain:flexymarkets.com';

    $startDate = date('Y-m-d', strtotime("-$days days"));
    $endDate = date('Y-m-d');

    $request = new Google\Service\Webmasters\SearchAnalyticsQueryRequest();
    $request->setStartDate($startDate);
    $request->setEndDate($endDate);
    $request->setDimensions(['query']);
    $request->setRowLimit(100);

    try {
        $response = $service->searchanalytics->query($siteUrl, $request);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to fetch data: ' . $e->getMessage()]);
        exit;
    }

    $totalClicks = 0;
    $totalImpressions = 0;
    $totalKeywords = 0;
    $totalPosition = 0;

    foreach ($response->getRows() as $row) {
        $totalClicks += $row->clicks;
        $totalImpressions += $row->impressions;
        $totalPosition += $row->position;
        $totalKeywords++;
    }

    return json_encode([
        'search_traffic' => $totalClicks,
        'search_impressions' => $totalImpressions,
        'total_keywords' => $totalKeywords,
        'avg_position' => $totalKeywords > 0 ? round($totalPosition / $totalKeywords, 2) : 0
    ]);
}

$days = isset($_GET['days']) ? intval($_GET['days']) : 7; // Default to 7 days
header('Content-Type: application/json');
echo getSearchConsoleData($days);
?>
