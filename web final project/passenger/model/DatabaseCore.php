<?php
// Dhaka Metro Rail - Centralized Production Database Engine
// Supports:
// 1. Production Cloud MySQL via Environment Variables (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD, MYSQL_SSL)
// 2. Production Cloud MySQL via Connection String (MYSQL_URL / DATABASE_URL)
// 3. Local Development MySQL (XAMPP 127.0.0.1:3306)
// 4. Explicit Offline Test Mode ONLY when DB_DRIVER=sqlite is deliberately provided

if (!defined('METRO_DB_INIT')) {
    define('METRO_DB_INIT', true);

    // Explicit opt-in SQLite mode strictly for isolated local tests
    if (getenv('DB_DRIVER') === 'sqlite') {
        require_once __DIR__ . '/DatabaseSqliteShim.php';
        $sqlitePath = sys_get_temp_dir() . '/dhaka_metro_test.sqlite';
        $conn = new MetroSqliteConn($sqlitePath);
        $db_connected = true;
        $GLOBALS['db_driver'] = 'sqlite';
    } else {
        // Detect local environment vs production
        $isVercel = (getenv('VERCEL') !== false || getenv('NOW_REGION') !== false);
        $hasCloudConfig = (getenv('DB_HOST') || getenv('MYSQL_HOST') || getenv('MYSQL_URL') || getenv('DATABASE_URL'));

        // Host configuration
        $servername = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: (!$isVercel ? '127.0.0.1' : '');
        $username   = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: (!$isVercel ? 'root' : '');
        $password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : (!$isVercel ? '' : ''));
        $database   = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: (!$isVercel ? 'mydb' : '');
        $port       = (int)(getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: 3306);

        // Support unified MySQL connection URLs (e.g. mysql://user:pass@host:port/dbname)
        if ($dbUrl = (getenv('MYSQL_URL') ?: getenv('DATABASE_URL'))) {
            $parsed = parse_url($dbUrl);
            if (!empty($parsed['host'])) $servername = $parsed['host'];
            if (!empty($parsed['user'])) $username   = $parsed['user'];
            if (isset($parsed['pass']))  $password   = $parsed['pass'];
            if (!empty($parsed['path'])) $database   = ltrim($parsed['path'], '/');
            if (!empty($parsed['port'])) $port       = (int)$parsed['port'];
        }

        $conn = false;
        $db_connected = false;
        $GLOBALS['db_connection_error'] = '';

        if (empty($servername)) {
            // Production environment without configured database credentials
            $GLOBALS['db_connection_error'] = "Production database is not configured. Please define DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME in Vercel Environment Variables.";
        } elseif (!extension_loaded('mysqli')) {
            $GLOBALS['db_connection_error'] = "PHP mysqli extension is required but not loaded in this environment.";
        } else {
            mysqli_report(MYSQLI_REPORT_OFF);
            $conn = @mysqli_init();

            if ($conn) {
                $flags = 0;
                $useSsl = (getenv('MYSQL_SSL') === 'true' || getenv('DB_SSL') === 'true' || strtolower((string)getenv('MYSQL_SSL')) === '1');
                if ($useSsl) {
                    $flags |= MYSQLI_CLIENT_SSL;
                    // Allow cloud CA certificates for managed cloud databases (TiDB Cloud, Aiven, Railway, AWS RDS, PlanetScale)
                    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
                }
                mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

                try {
                    $db_connected = @mysqli_real_connect($conn, $servername, $username, $password, $database, $port, null, $flags);
                    if ($db_connected) {
                        mysqli_set_charset($conn, 'utf8mb4');
                        $GLOBALS['db_driver'] = 'mysql';
                    } else {
                        $GLOBALS['db_connection_error'] = mysqli_connect_error() ?: "Failed to connect to MySQL database at {$servername}:{$port}.";
                        $conn = false;
                        $db_connected = false;
                    }
                } catch (Throwable $e) {
                    $GLOBALS['db_connection_error'] = $e->getMessage();
                    $conn = false;
                    $db_connected = false;
                }
            }
        }
    }

    if (!function_exists('hasDbConnection')) {
        function hasDbConnection() {
            global $conn, $db_connected;
            return ($conn !== false && $db_connected === true);
        }
    }
}
