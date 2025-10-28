<?php
// redirect.php - Validates token and displays content (multi-use for 5 hours)
$dbFile = '1/tokens.json';
$targetUrl = 'https://crichd-iframe-plr.blogspot.com/p/demo.html?m=1';
$homeUrl = 'https://asl-sports-1.blogspot.com/?m=1';

// Get token from URL
$token = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($token)) {
    showErrorAndRedirect('Invalid access. Please use the button to generate a new link.', $homeUrl);
    exit;
}

// Read tokens
$tokens = [];
if (file_exists($dbFile)) {
    $content = file_get_contents($dbFile);
    $tokens = json_decode($content, true);
    if ($tokens === null) {
        $tokens = [];
    }
}

// Check if token exists
if (!isset($tokens[$token])) {
    showErrorAndRedirect('Invalid or expired link. Please generate a new one.', $homeUrl);
    exit;
}

// Check if token has expired (5 hours)
$currentTime = time();
if ($currentTime > $tokens[$token]['expires']) {
    showErrorAndRedirect('This link has expired. Please generate a new one.', $homeUrl);
    exit;
}

// Function to show error and redirect
function showErrorAndRedirect($message, $redirectUrl) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="3;url=<?php echo htmlspecialchars($redirectUrl); ?>">
        <title>Error - Redirecting</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                text-align: center;
                max-width: 500px;
            }
            .error-icon {
                font-size: 60px;
                color: #ff6b6b;
                margin-bottom: 20px;
            }
            h1 {
                color: #333;
                font-size: 24px;
                margin-bottom: 15px;
            }
            p {
                color: #666;
                font-size: 16px;
                margin-bottom: 20px;
            }
            .countdown {
                font-size: 48px;
                font-weight: bold;
                color: #667eea;
                margin: 20px 0;
            }
            .redirect-text {
                font-size: 14px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1>Link Expired</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <div class="countdown" id="countdown">3</div>
            <p class="redirect-text">Redirecting to homepage...</p>
        </div>

        <script>
            let seconds = 3;
            const countdownElement = document.getElementById('countdown');
            
            const interval = setInterval(() => {
                seconds--;
                countdownElement.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '<?php echo htmlspecialchars($redirectUrl); ?>';
                }
            }, 1000);
        </script>
    </body>
    </html>
    <?php
}

// Token is valid - display content (no need to mark as used)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Viewer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body, html {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
    </style>
</head>
<body>
    <iframe src="<?php echo htmlspecialchars($targetUrl); ?>" 
            allowfullscreen 
            allow="autoplay; encrypted-media; picture-in-picture"
            sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-pointer-lock">
    </iframe>
    
    <script>
        // Prevent users from inspecting or accessing the iframe source
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Disable F12 and common inspect shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.keyCode == 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode == 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode == 67) || // Ctrl+Shift+C
                (e.ctrlKey && e.shiftKey && e.keyCode == 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode == 85)) { // Ctrl+U
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
