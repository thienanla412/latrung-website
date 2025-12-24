<?php
/**
 * Configuration file for La TRUNG Website
 * Loads environment variables and defines application settings
 */

// Load environment variables from .env file
function loadEnv($path = __DIR__ . '/.env') {
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse line
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    return true;
}

// Load .env file
loadEnv();

// Helper function to get environment variables with default values
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? $default;
    }

    // Convert string booleans to actual booleans
    if (is_string($value)) {
        $lower = strtolower($value);
        if ($lower === 'true') return true;
        if ($lower === 'false') return false;
        if ($lower === 'null') return null;
    }

    return $value;
}

// Application Configuration
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', false));

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'latrung_website'));
define('DB_USER', env('DB_USER', ''));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Email Configuration
define('MAIL_FROM_EMAIL', env('MAIL_FROM_EMAIL', 'info@latrungprint.vn'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'La TRUNG Printing & Packaging'));
define('MAIL_TO_EMAIL', env('MAIL_TO_EMAIL', 'info@latrungprint.vn'));

// SMTP Configuration
define('SMTP_HOST', env('SMTP_HOST', ''));
define('SMTP_PORT', env('SMTP_PORT', 587));
define('SMTP_USER', env('SMTP_USER', ''));
define('SMTP_PASS', env('SMTP_PASS', ''));
define('SMTP_ENCRYPTION', env('SMTP_ENCRYPTION', 'tls'));

// Security Configuration
define('SESSION_SECURE', env('SESSION_SECURE', true));
define('SESSION_HTTPONLY', env('SESSION_HTTPONLY', true));
define('SESSION_SAMESITE', env('SESSION_SAMESITE', 'Strict'));
define('CSRF_TOKEN_EXPIRE', env('CSRF_TOKEN_EXPIRE', 3600));

// Rate Limiting
define('RATE_LIMIT_ENABLED', env('RATE_LIMIT_ENABLED', true));
define('RATE_LIMIT_MAX_ATTEMPTS', env('RATE_LIMIT_MAX_ATTEMPTS', 3));
define('RATE_LIMIT_WINDOW', env('RATE_LIMIT_WINDOW', 3600));

// Site Configuration
define('SITE_URL', env('SITE_URL', 'https://www.latrungprint.vn'));
define('SITE_NAME', env('SITE_NAME', 'La TRUNG Printing & Packaging'));

// Logging Configuration
define('LOG_ENABLED', env('LOG_ENABLED', true));
define('LOG_PATH', env('LOG_PATH', __DIR__ . '/logs/'));
define('LOG_LEVEL', env('LOG_LEVEL', 'error'));

// Error Reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', LOG_PATH . 'php-errors.log');
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
