<?php
require 'vendor/autoload.php';

use Google\Client;

$client = new Client();
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setScopes(['https://www.googleapis.com/auth/webmasters.readonly']);
$client->setAccessType('offline');

// ✅ Get authorization code from Google
if (!isset($_GET['code'])) {
    die("❌ No authorization code received.");
}

$authCode = $_GET['code'];

// ✅ Exchange code for access token
$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
if (isset($accessToken['error'])) {
    die("❌ Error fetching token: " . $accessToken['error_description']);
}

// ✅ Save token to `token.json`
file_put_contents(__DIR__ . '/token.json', json_encode($accessToken, JSON_PRETTY_PRINT));

echo "✅ Success! Token saved to `token.json`.";
?>
