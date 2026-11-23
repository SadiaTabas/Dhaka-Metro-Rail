<?php
// Dhaka Metro Rail - Database Connection
// Supports Local XAMPP and Production Cloud MySQL via Environment Variables

// Suppress mysqli throwing exceptions so unconfigured production environments fail gracefully
mysqli_report(MYSQLI_REPORT_OFF);

$servername = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: '127.0.0.1';
$username   = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: 'root';
$password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : '');
$dbname     = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: 'mydb';
$port       = (int)(getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: 3306);

// Support cloud MySQL connection strings (e.g. Aiven, PlanetScale, TiDB, Supabase, Railway)
if ($dbUrl = (getenv('MYSQL_URL') ?: getenv('DATABASE_URL'))) {
    $parsed = parse_url($dbUrl);
    if (!empty($parsed['host']))     $servername = $parsed['host'];
    if (!empty($parsed['user']))     $username   = $parsed['user'];
    if (isset($parsed['pass']))      $password   = $parsed['pass'];
    if (!empty($parsed['path']))     $dbname     = ltrim($parsed['path'], '/');
    if (!empty($parsed['port']))     $port       = (int)$parsed['port'];
}

$conn = @mysqli_init();
$db_connected = false;

if ($conn) {
    if (getenv('MYSQL_SSL') === 'true' || getenv('DB_SSL') === 'true') {
        mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
    }
    
    // Set 5s connection timeout
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    try {
        $db_connected = @mysqli_real_connect($conn, $servername, $username, $password, $dbname, $port);
        if ($db_connected) {
            mysqli_set_charset($conn, 'utf8mb4');
        } else {
            $GLOBALS['db_connection_error'] = mysqli_connect_error();
            $conn = false;
        }
    } catch (Throwable $e) {
        $db_connected = false;
        $conn = false;
        $GLOBALS['db_connection_error'] = $e->getMessage();
    }
} else {
    $conn = false;
}

if (!function_exists('hasDbConnection')) {
    function hasDbConnection() {
        global $conn, $db_connected;
        return ($conn !== false && $db_connected === true);
    }
}
?>
