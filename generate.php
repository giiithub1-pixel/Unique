<?php
// generate.php - Generates one-time tokens
header('Content-Type: application/json');

// Database configuration
$dbFile = 'tokens.json';

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Read existing tokens
$tokens = [];
if (file_exists($dbFile)) {
    $content = file_get_contents($dbFile);
    $tokens = json_decode($content, true);
    if ($tokens === null) {
        $tokens = [];
    }
}

// Clean up expired tokens (older than 1 hour)
$currentTime = time();
$tokens = array_filter($tokens, function($token) use ($currentTime) {
    return ($currentTime - $token['created']) < 3600;
});

// Generate new token
$newToken = generateToken();
$tokens[$newToken] = [
    'created' => $currentTime,
    'used' => false
];

// Save tokens
file_put_contents($dbFile, json_encode($tokens));

// Return token
echo json_encode([
    'success' => true,
    'token' => $newToken
]);
?>