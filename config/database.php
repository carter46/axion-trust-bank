<?php
/**
 * Stand-in when a SELECT/SHOW query fails. Callers can always call fetch()/fetchAll()
 * without crashing on Database::query() returning false.
 */
class DbEmptyResult {
    public function fetch($mode = null) {
        return false;
    }
    public function fetchAll($mode = null, $args = null) {
        return [];
    }
    public function fetchColumn($column = 0) {
        return false;
    }
    public function rowCount() {
        return 0;
    }
    public function columnCount() {
        return 0;
    }
    public function execute($params = null) {
        return false;
    }
    public function errorInfo() {
        return ['HY000', 0, 'Query failed'];
    }
}

class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_TIMEOUT => 30,
                    PDO::ATTR_PERSISTENT => false
                ]
            );
            
            // Test the connection
            $this->conn->query("SELECT 1");
            
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            error_log("DB Host: " . DB_HOST);
            error_log("DB Name: " . DB_NAME);
            error_log("DB User: " . DB_USER);
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("Database query execution failed");
                error_log("SQL: " . $sql);
                error_log("Params: " . json_encode($params));
                error_log("PDO Error Code: " . ($errorInfo[0] ?? 'N/A'));
                error_log("Driver Error Code: " . ($errorInfo[1] ?? 'N/A'));
                error_log("Error Message: " . ($errorInfo[2] ?? 'N/A'));
                throw new PDOException($errorInfo[2] ?? 'Query execution failed', $errorInfo[1] ?? 0);
            }
            
            return $stmt;
        } catch(PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            error_log("PDO Error Code: " . $e->getCode());
            error_log("Error Info: " . print_r($this->conn->errorInfo(), true));
            if (self::isReadQuery($sql)) {
                return new DbEmptyResult();
            }
            return false;
        } catch (Throwable $e) {
            error_log("Database query error: " . $e->getMessage());
            if (self::isReadQuery($sql)) {
                return new DbEmptyResult();
            }
            return false;
        }
    }

    public static function isReadQuery($sql): bool
    {
        return (bool)preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN|WITH)\b/i', (string)$sql);
    }

    public static function isUsableStatement($stmt): bool
    {
        return is_object($stmt) && method_exists($stmt, 'fetch');
    }

    public function fetchRow($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        if (!self::isUsableStatement($stmt)) {
            return null;
        }
        try {
            $row = $stmt->fetch();
            return is_array($row) ? $row : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public function fetchAllRows($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        if (!self::isUsableStatement($stmt)) {
            return [];
        }
        try {
            $rows = $stmt->fetchAll();
            return is_array($rows) ? $rows : [];
        } catch (Throwable $e) {
            return [];
        }
    }
    
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    public function errorInfo() {
        return $this->conn->errorInfo();
    }
    
    // Transaction methods
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    public function commit() {
        return $this->conn->commit();
    }
    
    public function rollback() {
        return $this->conn->rollBack();
    }
    
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
