<?php
require 'vendor/autoload.php';

use Google\Client;

$client = new Client();
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setScopes(['https://www.googleapis.com/auth/webmasters.readonly']);
$client->setAccessType('offline'); 
$client->setPrompt('consent');

// ✅ Fix: Set the correct redirect URI
$client->setRedirectUri('http://localhost/blog-dashboard/api/oauth_callback.php');


// ✅ Fix: Display correct OAuth URL
$authUrl = $client->createAuthUrl();
echo "🔗 Open this URL in your browser:\n$authUrl\n";

// ✅ Wait for user input
echo "\n✏️  Paste the authorization code here: ";
$authCode = trim(fgets(STDIN));

try {
    // ✅ Fix: Exchange code for access token
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    if (isset($accessToken['error'])) {
        throw new Exception("❌ Error fetching access token: " . $accessToken['error_description']);
    }

    $client->setAccessToken($accessToken);

    // ✅ Fix: Save token only if valid
    if (!isset($accessToken['access_token'])) {
        throw new Exception("❌ Invalid access token received.");
    }

    // ✅ Save token to token.json
    $tokenPath = __DIR__ . '/token.json';
    file_put_contents($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));

    echo "\n✅ Token saved successfully to token.json!\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
?>
