<?php
// Dhaka Metro Rail - Unified Database Engine
// Supports:
// 1. Production Cloud MySQL via Environment Variables (DB_HOST, MYSQL_URL, etc.)
// 2. Local XAMPP MySQL (127.0.0.1:3306)
// 3. Automated Resilient SQLite Engine with Auto-Schema Migration & Seed Data for Serverless

if (!defined('METRO_DB_INIT')) {
    define('METRO_DB_INIT', true);

    class MetroSqliteResult {
        public $num_rows = 0;
        private $rows = [];
        private $index = 0;

        public function __construct(array $rows = []) {
            $this->rows = array_values($rows);
            $this->num_rows = count($this->rows);
            $this->index = 0;
        }

        public function fetch_assoc() {
            if ($this->index < $this->num_rows) {
                return $this->rows[$this->index++];
            }
            return null;
        }

        public function fetch_row() {
            if ($this->index < $this->num_rows) {
                return array_values($this->rows[$this->index++]);
            }
            return null;
        }

        public function fetch_all($mode = 1) { // 1 = MYSQLI_NUM, 2 = MYSQLI_ASSOC
            $result = [];
            while ($row = ($mode === 2 ? $this->fetch_assoc() : $this->fetch_row())) {
                $result[] = $row;
            }
            return $result;
        }

        public function free() {}
        public function close() {}
    }

    class MetroSqliteStmt {
        public $num_rows = 0;
        public $insert_id = 0;
        public $error = '';
        public $errno = 0;

        private $pdo;
        private $sql;
        private $connRef;
        private $params = [];
        private $cachedRows = [];

        public function __construct(PDO $pdo, string $sql, $connRef) {
            $this->pdo = $pdo;
            $cleanSql = preg_replace('/\bINSERT\s+IGNORE\s+INTO\b/i', 'INSERT OR IGNORE INTO', $sql);
            $this->sql = $cleanSql;
            $this->connRef = $connRef;
        }

        public function bind_param(string $types, &...$args) {
            $this->params = [];
            foreach ($args as &$arg) {
                $this->params[] = $arg;
            }
            return true;
        }

        public function execute() {
            try {
                $stmt = $this->pdo->prepare($this->sql);
                $success = $stmt->execute($this->params);
                if (!$success) {
                    $err = $stmt->errorInfo();
                    $this->error = $err[2] ?? 'Execution error';
                    $this->errno = 1;
                    return false;
                }
                $this->insert_id = (int)$this->pdo->lastInsertId();
                if ($this->connRef) {
                    $this->connRef->insert_id = $this->insert_id;
                }

                if ($stmt->columnCount() > 0) {
                    $this->cachedRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $this->num_rows = count($this->cachedRows);
                } else {
                    $this->cachedRows = [];
                    $this->num_rows = $stmt->rowCount();
                }
                return true;
            } catch (Throwable $e) {
                $this->error = $e->getMessage();
                $this->errno = 1;
                return false;
            }
        }

        public function store_result() {
            return true;
        }

        public function get_result() {
            return new MetroSqliteResult($this->cachedRows);
        }

        public function close() {
            $this->cachedRows = [];
            $this->params = [];
        }
    }

    class MetroSqliteConn {
        public $insert_id = 0;
        public $error = '';
        public $errno = 0;
        public $connect_errno = 0;
        public $connect_error = '';
        private $pdo;

        public function __construct(string $dbPath) {
            $this->pdo = new PDO("sqlite:$dbPath");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("PRAGMA journal_mode = WAL;");
            $this->initSchema();
        }

        public function prepare(string $sql) {
            return new MetroSqliteStmt($this->pdo, $sql, $this);
        }

        public function query(string $sql) {
            try {
                $cleanSql = preg_replace('/\bINSERT\s+IGNORE\s+INTO\b/i', 'INSERT OR IGNORE INTO', $sql);
                $stmt = $this->pdo->query($cleanSql);
                if (!$stmt) return false;

                if ($stmt->columnCount() > 0) {
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return new MetroSqliteResult($rows);
                }
                $this->insert_id = (int)$this->pdo->lastInsertId();
                return true;
            } catch (Throwable $e) {
                $this->error = $e->getMessage();
                $this->errno = 1;
                return false;
            }
        }

        public function begin_transaction() {
            return $this->pdo->beginTransaction();
        }

        public function commit() {
            return $this->pdo->commit();
        }

        public function rollback() {
            return $this->pdo->rollBack();
        }

        public function set_charset(string $charset) {
            return true;
        }

        public function close() {
            return true;
        }

        private function initSchema() {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS users (
                    UserID INTEGER PRIMARY KEY AUTOINCREMENT,
                    RoleID INTEGER NOT NULL DEFAULT 3,
                    FirstName TEXT NOT NULL,
                    LastName TEXT NOT NULL DEFAULT '',
                    Mobile TEXT NOT NULL UNIQUE,
                    NID TEXT DEFAULT NULL,
                    DateOfBirth TEXT DEFAULT NULL,
                    Gender TEXT DEFAULT NULL,
                    Password TEXT NOT NULL,
                    Status TEXT NOT NULL DEFAULT 'Active',
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS stations (
                    StationID INTEGER PRIMARY KEY AUTOINCREMENT,
                    StationName TEXT NOT NULL UNIQUE,
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS routes (
                    RouteID INTEGER PRIMARY KEY AUTOINCREMENT,
                    FromStation TEXT NOT NULL,
                    ToStation TEXT NOT NULL,
                    Fare REAL NOT NULL,
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(FromStation, ToStation)
                );

                CREATE TABLE IF NOT EXISTS wallet (
                    WalletID INTEGER PRIMARY KEY AUTOINCREMENT,
                    UserID INTEGER NOT NULL UNIQUE,
                    Balance REAL NOT NULL DEFAULT 0.00,
                    UpdatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS tickets (
                    TicketID INTEGER PRIMARY KEY AUTOINCREMENT,
                    UserID INTEGER NOT NULL,
                    RouteID INTEGER NOT NULL,
                    JourneyDate TEXT NOT NULL,
                    Status TEXT NOT NULL DEFAULT 'Confirmed',
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS payments (
                    PaymentID INTEGER PRIMARY KEY AUTOINCREMENT,
                    TicketID INTEGER NOT NULL,
                    Amount REAL NOT NULL,
                    PaymentDate TEXT DEFAULT CURRENT_TIMESTAMP,
                    Status TEXT NOT NULL DEFAULT 'Success'
                );

                CREATE TABLE IF NOT EXISTS transactions (
                    TransactionID INTEGER PRIMARY KEY AUTOINCREMENT,
                    UserID INTEGER NOT NULL,
                    Amount REAL NOT NULL,
                    Type TEXT NOT NULL,
                    Reference TEXT NOT NULL,
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS schedules (
                    ScheduleID INTEGER PRIMARY KEY AUTOINCREMENT,
                    TrainName TEXT NOT NULL,
                    RouteID INTEGER DEFAULT NULL,
                    DepartureTime TEXT NOT NULL,
                    ArrivalTime TEXT NOT NULL,
                    CreatedAt TEXT DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS ticket_validations (
                    ValidationID INTEGER PRIMARY KEY AUTOINCREMENT,
                    TicketID INTEGER NOT NULL,
                    Status TEXT NOT NULL DEFAULT 'Valid',
                    ValidatedBy INTEGER DEFAULT NULL,
                    ValidationTime TEXT DEFAULT CURRENT_TIMESTAMP
                );
            ");

            // Seed Stations
            $stations = [
                "Uttara North", "Uttara Center", "Uttara South", "Pallabi",
                "Mirpur-11", "Mirpur-10", "Kazipara", "Shewrapara",
                "Agargaon", "Bijoy Sarani", "Farmgate", "Karwan Bazar",
                "Shahbagh", "Dhaka University", "Secretariat", "Motijheel", "Kamalapur"
            ];
            $stStmt = $this->pdo->prepare("INSERT OR IGNORE INTO stations (StationName) VALUES (?)");
            foreach ($stations as $st) {
                $stStmt->execute([$st]);
            }

            // Seed Standard Routes between stations
            $rStmt = $this->pdo->prepare("INSERT OR IGNORE INTO routes (FromStation, ToStation, Fare) VALUES (?, ?, ?)");
            $count = count($stations);
            for ($i = 0; $i < $count; $i++) {
                for ($j = 0; $j < $count; $j++) {
                    if ($i !== $j) {
                        $dist = abs($i - $j);
                        $fare = min(100.00, max(20.00, 20.00 + ($dist - 1) * 10.00));
                        $rStmt->execute([$stations[$i], $stations[$j], $fare]);
                    }
                }
            }

            // Seed Users (Admin: 01711000001 / admin123, Employee: 01711000002 / employee123, Passenger: 01711000003 / pass123)
            $uStmt = $this->pdo->prepare("INSERT OR IGNORE INTO users (UserID, RoleID, FirstName, LastName, Mobile, NID, DateOfBirth, Gender, Password, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            $uStmt->execute([1, 1, 'System', 'Admin', '01711000001', '1990123456789', '1990-01-01', 'Male', password_hash('admin123', PASSWORD_DEFAULT)]);
            $uStmt->execute([2, 2, 'Rafiqul', 'Islam', '01711000002', '1992123456789', '1992-05-10', 'Male', password_hash('employee123', PASSWORD_DEFAULT)]);
            $uStmt->execute([3, 3, 'Sadia', 'Tabassum', '01711000003', '1998269251234', '2000-01-15', 'Female', password_hash('pass123', PASSWORD_DEFAULT)]);

            // Seed Wallets
            $wStmt = $this->pdo->prepare("INSERT OR IGNORE INTO wallet (UserID, Balance) VALUES (?, ?)");
            $wStmt->execute([1, 1000.00]);
            $wStmt->execute([2, 500.00]);
            $wStmt->execute([3, 500.00]);

            // Seed Initial Ticket & Transaction for Passenger
            $tStmt = $this->pdo->prepare("INSERT OR IGNORE INTO tickets (TicketID, UserID, RouteID, JourneyDate, Status) VALUES (?, ?, ?, ?, ?)");
            $tStmt->execute([1, 3, 1, date('Y-m-d'), 'Confirmed']);

            $txStmt = $this->pdo->prepare("INSERT OR IGNORE INTO transactions (TransactionID, UserID, Amount, Type, Reference) VALUES (?, ?, ?, ?, ?)");
            $txStmt->execute([1, 3, 500.00, 'Credit', 'Welcome Top-up']);

            // Seed Schedules
            $sStmt = $this->pdo->prepare("INSERT OR IGNORE INTO schedules (ScheduleID, TrainName, RouteID, DepartureTime, ArrivalTime) VALUES (?, ?, ?, ?, ?)");
            $sStmt->execute([1, 'MRT-01 (Red Line)', 1, '07:30:00', '08:05:00']);
            $sStmt->execute([2, 'MRT-02 (Green Line)', 2, '08:00:00', '08:45:00']);
            $sStmt->execute([3, 'MRT-03 (Blue Line)', 3, '09:00:00', '09:40:00']);
        }
    }

    // Suppress unhandled exceptions
    mysqli_report(MYSQLI_REPORT_OFF);

    $servername = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: '127.0.0.1';
    $username   = getenv('DB_USER') ?: getenv('MYSQL_USER') ?: 'root';
    $password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : '');
    $database   = getenv('DB_NAME') ?: getenv('MYSQL_DATABASE') ?: 'mydb';
    $port       = (int)(getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: 3306);

    if ($dbUrl = (getenv('MYSQL_URL') ?: getenv('DATABASE_URL'))) {
        $parsed = parse_url($dbUrl);
        if (!empty($parsed['host'])) $servername = $parsed['host'];
        if (!empty($parsed['user'])) $username   = $parsed['user'];
        if (isset($parsed['pass']))  $password   = $parsed['pass'];
        if (!empty($parsed['path'])) $database   = ltrim($parsed['path'], '/');
        if (!empty($parsed['port'])) $port       = (int)$parsed['port'];
    }

    $conn = @mysqli_init();
    $db_connected = false;

    if ($conn) {
        if (getenv('MYSQL_SSL') === 'true' || getenv('DB_SSL') === 'true') {
            mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
        }
        mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 2);

        try {
            $db_connected = @mysqli_real_connect($conn, $servername, $username, $password, $database, $port);
            if ($db_connected) {
                mysqli_set_charset($conn, 'utf8mb4');
                $GLOBALS['db_driver'] = 'mysql';
            } else {
                $conn = false;
            }
        } catch (Throwable $e) {
            $conn = false;
        }
    }

    // Resilient Fallback to SQLite if MySQL is unreachable
    if (!$db_connected || !$conn) {
        try {
            $sqlitePath = sys_get_temp_dir() . '/dhaka_metro_db.sqlite';
            $conn = new MetroSqliteConn($sqlitePath);
            $db_connected = true;
            $GLOBALS['db_driver'] = 'sqlite';
        } catch (Throwable $e) {
            $conn = false;
            $db_connected = false;
            $GLOBALS['db_connection_error'] = $e->getMessage();
        }
    }

    if (!function_exists('hasDbConnection')) {
        function hasDbConnection() {
            global $conn, $db_connected;
            return ($conn !== false && $db_connected === true);
        }
    }
}
