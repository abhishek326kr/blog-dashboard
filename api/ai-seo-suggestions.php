<?php
session_start();
require_once '../config/db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Validate session and headers
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized: Session not found']));
}

// Check if the CSRF token header exists
$headers = getallheaders(); // Get all headers
$csrfToken = $headers['X-CSRF-TOKEN'] ?? null;

if (!$csrfToken || $csrfToken !== $_SESSION['csrf_token']) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized: Invalid CSRF token']));
}

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$content = strip_tags(trim($data['content'] ?? ''));

if (empty($title)) {
    http_response_code(400);
    exit(json_encode(['error' => 'Title is required']));
}

try {
    // Hardcoded API key (not recommended for production)
    $apiKey = 'sk-6e44d81440504ff08330bed63b9baad1';

    

    // Improved Prompt for Deepseek API
    $prompt = "Generate high-quality SEO metadata for a blog post titled: '$title'\n\n";
    $prompt .= "Content: " . substr($content, 0, 1000) . "\n\n"; // Use first 1000 characters of content
    $prompt .= "Provide the following in JSON format:\n";
    $prompt .= "- seoTitle: A compelling SEO title under 60 characters, including primary keywords.\n";
    $prompt .= "- seoDescription: A concise meta description under 160 characters, summarizing the content.\n";
    $prompt .= "- seoKeywords: 5 relevant, comma-separated keywords.\n";
    $prompt .= "- seoSlug: A URL-friendly slug based on the title.\n";
    $prompt .= "- canonicalUrl: The canonical URL for the post.\n";
    $prompt .= "- metaRobots: Either 'index,follow' or 'noindex,follow'.\n";
    $prompt .= "- ogTitle: An engaging Open Graph title under 60 characters.\n";
    $prompt .= "- ogDescription: A short Open Graph description under 160 characters.\n";
    $prompt .= "Ensure all suggestions are optimized for search engines and social media.";

    // Deepseek API Call
    $ch = curl_init('https://api.deepseek.com/v1/chat/completions');
    $payload = json_encode([
        'model' => 'deepseek-chat',
        'messages' => [[
            'role' => 'user',
            'content' => $prompt
        ]],
        'temperature' => 0.7,
        'max_tokens' => 500
    ]);

    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Content-Type: 'application/json',
            'Authorization: Bearer ' . $apiKey // Use the hardcoded API key
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($status !== 200 || !$response) {
        throw new Exception("API Error: $status");
    }

    $ai_data = json_decode($response, true);
    $content = json_decode($ai_data['choices'][0]['message']['content'], true);

    // Fallback Logic if API Response is Invalid
    if (empty($content) {
        throw new Exception("Invalid API response format");
    }

    // Sanitize and validate response
    $result = [
        'seoTitle' => substr($content['seoTitle'] ?? generateSeoTitle($title), 0, 60),
        'seoDescription' => substr($content['seoDescription'] ?? generateSeoDescription($content), 0, 160),
        'seoKeywords' => substr(implode(', ', array_slice(explode(',', $content['seoKeywords'] ?? generateSeoKeywords($title, $content)), 0, 5)), 0, 255),
        'seoSlug' => substr(preg_replace('/[^a-z0-9-]/', '', strtolower($content['seoSlug'] ?? generateSeoSlug($title))), 0, 100),
        'canonicalUrl' => filter_var($content['canonicalUrl'] ?? generateCanonicalUrl($title), FILTER_SANITIZE_URL),
        'metaRobots' => in_array($content['metaRobots'] ?? '', ['index,follow', 'noindex,follow']) ? 
                        $content['metaRobots'] : 'index,follow',
        'ogTitle' => substr($content['ogTitle'] ?? generateOgTitle($title), 0, 60),
        'ogDescription' => substr($content['ogDescription'] ?? generateOgDescription($content), 0, 160)
    ];

    echo json_encode($result);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate suggestions: ' . $e->getMessage()]);
}

/**
 * Fallback Functions
 */

function generateSeoTitle($title) {
    return "Best " . $title . " Guide | YourSite.com";
}

function generateSeoDescription($content) {
    return substr(strip_tags($content), 0, 150) . "...";
}

function generateSeoKeywords($title, $content) {
    $keywords = array_unique(array_merge(
        explode(' ', preg_replace('/[^a-zA-Z ]/', '', strtolower($title))),
        explode(' ', preg_replace('/[^a-zA-Z ]/', '', strtolower($content)))
    ));
    return implode(', ', array_slice($keywords, 0, 5));
}

function generateSeoSlug($title) {
    return preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $title)));
}

function generateCanonicalUrl($title) {
    return "https://yoursite.com/blog/" . generateSeoSlug($title);
}

function generateOgTitle($title) {
    return "Discover: " . $title;
}

function generateOgDescription($content) {
    return substr(strip_tags($content), 0, 150) . "...";
}