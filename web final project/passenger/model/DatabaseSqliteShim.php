<?php
// Dhaka Metro Rail - Offline SQLite Testing Shim
// ONLY loaded when explicitly requested via DB_DRIVER=sqlite for isolated CLI unit testing.
// Never used as a silent fallback in production.

if (!class_exists('MetroSqliteResult')) {
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

        public function fetch_all($mode = 1) {
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
            try {
                $this->pdo->exec("PRAGMA journal_mode = DELETE;");
                $this->pdo->exec("PRAGMA synchronous = NORMAL;");
            } catch (Throwable $e) {}
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
    }
}
