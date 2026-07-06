<?php
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
            return false;
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
