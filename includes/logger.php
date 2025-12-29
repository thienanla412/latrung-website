<?php
/**
 * Logging System
 * Centralized logging for application events, errors, and security incidents
 */

require_once __DIR__ . '/../config.php';

class Logger {
    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_CRITICAL = 'CRITICAL';

    /**
     * Log a message
     *
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context data
     * @param string $logFile Optional specific log file
     */
    public static function log($level, $message, $context = [], $logFile = null) {
        if (!LOG_ENABLED) {
            return;
        }

        // Check if level should be logged based on config
        if (!self::shouldLog($level)) {
            return;
        }

        // Determine log file
        if ($logFile === null) {
            $logFile = LOG_PATH . 'application.log';
        } else {
            $logFile = LOG_PATH . $logFile;
        }

        // Create log directory if it doesn't exist
        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        // Format log message
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextString}\n";

        // Write to log file
        error_log($logMessage, 3, $logFile);

        // Also log critical errors to PHP error log
        if ($level === self::LEVEL_CRITICAL) {
            error_log("[CRITICAL] {$message}");
        }
    }

    /**
     * Check if a log level should be logged based on configuration
     */
    private static function shouldLog($level) {
        $configLevel = strtoupper(LOG_LEVEL);
        $levels = [
            self::LEVEL_DEBUG => 0,
            self::LEVEL_INFO => 1,
            self::LEVEL_WARNING => 2,
            self::LEVEL_ERROR => 3,
            self::LEVEL_CRITICAL => 4
        ];

        $currentLevelValue = $levels[$level] ?? 1;
        $configLevelValue = $levels[$configLevel] ?? 3;

        return $currentLevelValue >= $configLevelValue;
    }

    /**
     * Log debug message
     */
    public static function debug($message, $context = []) {
        self::log(self::LEVEL_DEBUG, $message, $context);
    }

    /**
     * Log info message
     */
    public static function info($message, $context = []) {
        self::log(self::LEVEL_INFO, $message, $context);
    }

    /**
     * Log warning message
     */
    public static function warning($message, $context = []) {
        self::log(self::LEVEL_WARNING, $message, $context);
    }

    /**
     * Log error message
     */
    public static function error($message, $context = []) {
        self::log(self::LEVEL_ERROR, $message, $context);
    }

    /**
     * Log critical error message
     */
    public static function critical($message, $context = []) {
        self::log(self::LEVEL_CRITICAL, $message, $context);
    }

    /**
     * Log form submission
     */
    public static function logFormSubmission($formName, $data = []) {
        $message = "Form submitted: {$formName}";
        self::info($message, $data);
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent($event, $context = []) {
        $message = "Security event: {$event}";
        self::log(self::LEVEL_WARNING, $message, $context, 'security.log');
    }

    /**
     * Log database query
     */
    public static function logDatabaseQuery($query, $params = [], $error = null) {
        if ($error) {
            $message = "Database error: {$error} | Query: {$query}";
            self::log(self::LEVEL_ERROR, $message, ['params' => $params], 'database.log');
        } elseif (APP_DEBUG) {
            $message = "Query executed: {$query}";
            self::log(self::LEVEL_DEBUG, $message, ['params' => $params], 'database.log');
        }
    }

    /**
     * Log email sent
     */
    public static function logEmail($to, $subject, $status, $error = null) {
        $level = $status === 'success' ? self::LEVEL_INFO : self::LEVEL_ERROR;
        $message = "Email {$status}: To: {$to} | Subject: {$subject}";
        $context = $error ? ['error' => $error] : [];
        self::log($level, $message, $context, 'email.log');
    }

    /**
     * Clean old log files (run this periodically via cron)
     */
    public static function cleanOldLogs($daysToKeep = 30) {
        if (!is_dir(LOG_PATH)) {
            return;
        }

        $files = glob(LOG_PATH . '*.log');
        $cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }

    /**
     * Rotate log file if it gets too large
     */
    public static function rotateLogIfNeeded($logFile, $maxSize = 10485760) { // 10MB default
        $fullPath = LOG_PATH . $logFile;

        if (!file_exists($fullPath)) {
            return;
        }

        if (filesize($fullPath) > $maxSize) {
            $rotatedFile = LOG_PATH . pathinfo($logFile, PATHINFO_FILENAME) . '_' . date('Y-m-d_H-i-s') . '.log';
            rename($fullPath, $rotatedFile);
        }
    }
}

/**
 * Helper functions for quick logging
 */
function logDebug($message, $context = []) {
    Logger::debug($message, $context);
}

function logInfo($message, $context = []) {
    Logger::info($message, $context);
}

function logWarning($message, $context = []) {
    Logger::warning($message, $context);
}

function logError($message, $context = []) {
    Logger::error($message, $context);
}

function logCritical($message, $context = []) {
    Logger::critical($message, $context);
}
