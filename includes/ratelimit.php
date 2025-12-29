<?php
/**
 * Rate Limiting System
 * Prevents spam and abuse of forms
 */

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/../config.php';

class RateLimit {
    private static $storage = [];

    /**
     * Check if rate limit is exceeded
     *
     * @param string $identifier Unique identifier (IP, email, etc.)
     * @param string $action Action being rate limited
     * @return bool True if allowed, false if rate limited
     */
    public static function check($identifier, $action = 'default') {
        if (!RATE_LIMIT_ENABLED) {
            return true;
        }

        initSecureSession();

        $key = self::getKey($identifier, $action);
        $attempts = self::getAttempts($key);

        if ($attempts >= RATE_LIMIT_MAX_ATTEMPTS) {
            $resetTime = self::getResetTime($key);
            if (time() < $resetTime) {
                self::logRateLimit($identifier, $action, $attempts);
                return false;
            } else {
                // Reset counter
                self::reset($key);
                return true;
            }
        }

        return true;
    }

    /**
     * Record an attempt
     */
    public static function record($identifier, $action = 'default') {
        if (!RATE_LIMIT_ENABLED) {
            return;
        }

        initSecureSession();

        $key = self::getKey($identifier, $action);

        if (!isset($_SESSION['rate_limit'])) {
            $_SESSION['rate_limit'] = [];
        }

        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 0,
                'reset_time' => time() + RATE_LIMIT_WINDOW
            ];
        }

        $_SESSION['rate_limit'][$key]['attempts']++;
    }

    /**
     * Get number of attempts
     */
    private static function getAttempts($key) {
        initSecureSession();

        if (!isset($_SESSION['rate_limit'][$key])) {
            return 0;
        }

        // Check if window has expired
        if (time() >= $_SESSION['rate_limit'][$key]['reset_time']) {
            self::reset($key);
            return 0;
        }

        return $_SESSION['rate_limit'][$key]['attempts'];
    }

    /**
     * Get reset time
     */
    private static function getResetTime($key) {
        initSecureSession();

        if (!isset($_SESSION['rate_limit'][$key])) {
            return time();
        }

        return $_SESSION['rate_limit'][$key]['reset_time'];
    }

    /**
     * Reset rate limit for a key
     */
    public static function reset($key) {
        initSecureSession();

        if (isset($_SESSION['rate_limit'][$key])) {
            unset($_SESSION['rate_limit'][$key]);
        }
    }

    /**
     * Generate unique key
     */
    private static function getKey($identifier, $action) {
        return md5($identifier . '_' . $action);
    }

    /**
     * Get remaining time until reset
     */
    public static function getRemainingTime($identifier, $action = 'default') {
        $key = self::getKey($identifier, $action);
        $resetTime = self::getResetTime($key);
        $remaining = $resetTime - time();

        return max(0, $remaining);
    }

    /**
     * Get remaining attempts
     */
    public static function getRemainingAttempts($identifier, $action = 'default') {
        $key = self::getKey($identifier, $action);
        $attempts = self::getAttempts($key);

        return max(0, RATE_LIMIT_MAX_ATTEMPTS - $attempts);
    }

    /**
     * Log rate limit violation
     */
    private static function logRateLimit($identifier, $action, $attempts) {
        if (!LOG_ENABLED) {
            return;
        }

        $logFile = LOG_PATH . 'ratelimit.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] Rate limit exceeded - Identifier: {$identifier}, Action: {$action}, Attempts: {$attempts}\n";

        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        error_log($logMessage, 3, $logFile);
    }
}

/**
 * Helper function to check rate limit
 */
function checkRateLimit($identifier, $action = 'default') {
    return RateLimit::check($identifier, $action);
}

/**
 * Helper function to record rate limit attempt
 */
function recordRateLimit($identifier, $action = 'default') {
    RateLimit::record($identifier, $action);
}
