<?php
/**
 * Database connection handler with PDO
 * Provides secure database connections and error handling
 */

// Require configuration
require_once __DIR__ . '/../config.php';

class Database {
    private static $instance = null;
    private $connection = null;
    private $lastError = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->logError("Database connection failed: " . $e->getMessage());

            // In production, don't expose database errors
            if (!APP_DEBUG) {
                throw new Exception("Database connection failed. Please try again later.");
            } else {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        // Check if connection is still alive
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Execute a query and return results
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            $this->logError("Query failed: " . $e->getMessage() . " | SQL: " . $sql);

            if (!APP_DEBUG) {
                throw new Exception("Database query failed. Please try again later.");
            } else {
                throw new Exception("Query failed: " . $e->getMessage());
            }
        }
    }

    /**
     * Insert data and return last insert ID
     */
    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        $this->query($sql, $data);
        return $this->connection->lastInsertId();
    }

    /**
     * Update data
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key} = :{$key}";
        }
        $setString = implode(', ', $set);

        $sql = "UPDATE {$table} SET {$setString} WHERE {$where}";

        $params = array_merge($data, $whereParams);
        return $this->query($sql, $params);
    }

    /**
     * Delete data
     */
    public function delete($table, $where, $whereParams = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $whereParams);
    }

    /**
     * Get last error
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollBack();
    }

    /**
     * Log database errors
     */
    private function logError($message) {
        if (LOG_ENABLED) {
            $logFile = LOG_PATH . 'database.log';
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$message}\n";

            // Create log directory if it doesn't exist
            if (!is_dir(LOG_PATH)) {
                mkdir(LOG_PATH, 0755, true);
            }

            error_log($logMessage, 3, $logFile);
        }
    }

    /**
     * Prevent cloning of singleton
     */
    private function __clone() {}

    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database instance
 */
function db() {
    return Database::getInstance();
}
