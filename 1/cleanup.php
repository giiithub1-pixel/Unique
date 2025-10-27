<?php
// cleanup.php - Clean tokens older than 5 hours
$dbFile = '1/tokens.json';

if (file_exists($dbFile)) {
    $content = file_get_contents($dbFile);
    $tokens = json_decode($content, true);
    
    if ($tokens === null) {
        $tokens = [];
    }
    
    $currentTime = time();
    $tokens = array_filter($tokens, function($token) use ($currentTime) {
        return ($currentTime - $token['created']) < 18000; // Keep tokens less than 5 hours old
    });
    
    file_put_contents($dbFile, json_encode($tokens));
    echo "Cleanup completed. Remaining tokens: " . count($tokens);
} else {
    echo "No token file found.";
}
?>
