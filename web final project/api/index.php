<?php
// Dhaka Metro Rail - Vercel Serverless Function Gateway & Unified Front-Controller Router

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath = parse_url($requestUri, PHP_URL_PATH);
$path = trim($parsedPath, '/');

// Parse query string into $_GET and $_REQUEST if not already populated
$queryString = parse_url($requestUri, PHP_URL_QUERY);
if (!empty($queryString)) {
    parse_str($queryString, $queryParams);
    $_GET = array_merge($_GET, $queryParams);
    $_REQUEST = array_merge($_REQUEST, $queryParams);
}

// Base directory is the application root
$baseDir = dirname(__DIR__);

// Ensure session save path is writable across all environments (Vercel Lambda /tmp, Windows %TEMP%)
$currentSavePath = session_save_path();
if (empty($currentSavePath) || !@is_writable($currentSavePath)) {
    $tempDir = sys_get_temp_dir();
    if (@is_writable($tempDir)) {
        @session_save_path($tempDir);
    }
}

// Handle Static Assets
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$staticExts = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'woff', 'woff2', 'ttf', 'eot'];

if (in_array($ext, $staticExts)) {
    $candidates = [
        $baseDir . '/' . $path,
        $baseDir . '/passenger/view/' . $path,
        $baseDir . '/admin/view/' . $path,
        $baseDir . '/employee/View/css/' . $path,
        $baseDir . '/employee/View/js/' . $path,
        $baseDir . '/employee/View/images/' . $path,
        $baseDir . '/employee/View/php/' . $path,
    ];

    foreach ($candidates as $cand) {
        if (file_exists($cand) && !is_dir($cand)) {
            $mimeTypes = [
                'css'   => 'text/css; charset=UTF-8',
                'js'    => 'application/javascript; charset=UTF-8',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'svg'   => 'image/svg+xml',
                'ico'   => 'image/x-icon',
                'webp'  => 'image/webp',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf'   => 'font/ttf',
                'eot'   => 'application/vnd.ms-fontobject',
            ];
            header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
            header('Cache-Control: public, max-age=86400');
            readfile($cand);
            exit();
        }
    }
}

// Convenient Shortcuts
if ($path === '' || $path === 'index.php') {
    // Primary Objective: Passenger Dashboard is the Homepage
    $targetFile = $baseDir . '/passenger/view/dashboard.php';
} elseif ($path === 'login' || $path === 'login.php') {
    $targetFile = $baseDir . '/employee/View/php/login.php';
} elseif ($path === 'signup' || $path === 'signup.php' || $path === 'register') {
    $targetFile = $baseDir . '/passenger/view/signup.php';
} elseif ($path === 'dashboard' || $path === 'dashboard.php') {
    $targetFile = $baseDir . '/passenger/view/dashboard.php';
} else {
    // Direct path check
    $targetFile = $baseDir . '/' . $path;

    if (!file_exists($targetFile) && file_exists($targetFile . '.php')) {
        $targetFile .= '.php';
    }

    // Module-relative fallback checks
    if (!file_exists($targetFile)) {
        $fallbacks = [
            $baseDir . '/passenger/' . $path,
            $baseDir . '/passenger/view/' . $path,
            $baseDir . '/passenger/controller/' . $path,
            $baseDir . '/admin/' . $path,
            $baseDir . '/admin/view/' . $path,
            $baseDir . '/admin/controller/' . $path,
            $baseDir . '/employee/' . $path,
            $baseDir . '/employee/View/php/' . $path,
            $baseDir . '/employee/Controller/' . $path,
        ];

        foreach ($fallbacks as $fb) {
            if (file_exists($fb) && !is_dir($fb)) {
                $targetFile = $fb;
                break;
            }
            if (file_exists($fb . '.php')) {
                $targetFile = $fb . '.php';
                break;
            }
        }
    }
}

// Execute target PHP file
if (file_exists($targetFile) && !is_dir($targetFile)) {
    $_SERVER['SCRIPT_FILENAME'] = realpath($targetFile);
    $relPath = str_replace([$baseDir, '\\'], ['', '/'], $targetFile);
    $_SERVER['SCRIPT_NAME'] = '/' . ltrim($relPath, '/');
    $_SERVER['PHP_SELF'] = '/' . ltrim($relPath, '/');

    chdir(dirname($targetFile));
    require $targetFile;
    exit();
}

// 404 Fallback
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Dhaka Metro Rail</title>
    <link rel="stylesheet" href="/passenger/view/dashboard.css">
    <style>
        body { background: #f7faf9; display: flex; align-items: center; justify-content: center; min-height: 100vh; text-align: center; }
        .error-card { background: #fff; padding: 40px; border-radius: 12px; border: 2px solid #006a4e; max-width: 500px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin: 20px; }
        h1 { color: #f42a41; font-size: 32px; margin-bottom: 12px; }
        p { color: #64748b; font-size: 15px; margin-bottom: 25px; line-height: 1.5; }
        .btn-home { display: inline-block; background: #006a4e; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; }
        .btn-home:hover { background: #004d38; }
    </style>
</head>
<body>
    <div class="error-card">
        <h1>404 - Page Not Found</h1>
        <p>The requested page <code><?= htmlspecialchars($requestUri) ?></code> could not be found on Dhaka Metro Rail.</p>
        <a href="/" class="btn-home">Return to Passenger Dashboard</a>
    </div>
</body>
</html>
