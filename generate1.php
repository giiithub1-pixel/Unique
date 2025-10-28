<?php
// generate.php - Generates tokens valid for 5 hours
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

// Clean up expired tokens (older than 5 hours)
$currentTime = time();
$tokens = array_filter($tokens, function($token) use ($currentTime) {
    return ($currentTime - $token['created']) < 18000; // 5 hours = 18000 seconds
});

// Generate new token
$newToken = generateToken();
$tokens[$newToken] = [
    'created' => $currentTime,
    'expires' => $currentTime + 18000 // Valid for 5 hours
];

// Save tokens
file_put_contents($dbFile, json_encode($tokens));

// Return token
echo json_encode([
    'success' => true,
    'token' => $newToken
]);
?>
