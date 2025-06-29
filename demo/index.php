<?php
// Demo PHP API - URL Shortener
// Shows Velocity deployment in action

// Simple in-memory storage (in production, use a database)
$dataFile = 'urls.json';

function loadUrls() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        return [];
    }
    return json_decode(file_get_contents($dataFile), true) ?: [];
}

function saveUrls($urls) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($urls, JSON_PRETTY_PRINT));
}

function generateShortCode() {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// API Routes
if ($method === 'POST' && $path === '/api/shorten') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $originalUrl = $input['url'] ?? '';
    
    if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid URL']);
        exit;
    }
    
    $urls = loadUrls();
    $shortCode = generateShortCode();
    
    $urls[$shortCode] = [
        'url' => $originalUrl,
        'created' => date('Y-m-d H:i:s'),
        'clicks' => 0
    ];
    
    saveUrls($urls);
    
    echo json_encode([
        'short_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $shortCode,
        'original_url' => $originalUrl,
        'code' => $shortCode
    ]);
    exit;
}

if ($method === 'GET' && preg_match('/^\/([a-zA-Z0-9]{6})$/', $path, $matches)) {
    // Redirect short URL
    $shortCode = $matches[1];
    $urls = loadUrls();
    
    if (isset($urls[$shortCode])) {
        $urls[$shortCode]['clicks']++;
        saveUrls($urls);
        
        header('Location: ' . $urls[$shortCode]['url']);
        exit;
    }
}

if ($method === 'GET' && $path === '/api/stats') {
    header('Content-Type: application/json');
    echo json_encode(loadUrls());
    exit;
}

// Homepage
?>
<!DOCTYPE html>
<html>
<head>
    <title>⚡ URL Shortener - Deployed with Velocity</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; }
        .form { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        .velocity-badge { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; margin: 20px 0; }
        input, button { padding: 12px; margin: 5px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; cursor: pointer; border: none; }
        button:hover { background: #0056b3; }
        .result { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚡ URL Shortener</h1>
        <div class="velocity-badge">🚀 Deployed with Velocity in seconds</div>
    </div>
    
    <div class="form">
        <h3>Shorten a URL</h3>
        <input type="url" id="url" placeholder="https://example.com" style="width: 70%;">
        <button onclick="shortenUrl()">Shorten</button>
        <div id="result"></div>
    </div>
    
    <div>
        <h3>How this was deployed:</h3>
        <pre># Traditional PHP deployment nightmare:
# 1. Configure nginx manually
# 2. Set up PHP-FPM 
# 3. Write deployment scripts
# 4. Handle environment variables
# 5. Set up monitoring
# 6. Debug deployment issues

# With Velocity (built by a 15-year-old):
php velocity-standalone.php init
php velocity-standalone.php deploy
# Done! 🎉</pre>
    </div>

    <script>
        async function shortenUrl() {
            const url = document.getElementById('url').value;
            if (!url) {
                alert('Please enter a URL');
                return;
            }
            
            try {
                const response = await fetch('/api/shorten', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ url })
                });
                
                const result = await response.json();
                
                if (result.error) {
                    document.getElementById('result').innerHTML = 
                        `<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">Error: ${result.error}</div>`;
                } else {
                    document.getElementById('result').innerHTML = 
                        `<div class="result">Short URL: <a href="${result.short_url}" target="_blank">${result.short_url}</a></div>`;
                }
            } catch (error) {
                document.getElementById('result').innerHTML = 
                    `<div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;">Error: ${error.message}</div>`;
            }
        }
    </script>
</body>
</html>
