<?php
/**
 * Secure session configuration
 * Implements best practices for session security
 */

require_once __DIR__ . '/../config.php';

/**
 * Initialize secure session
 */
function initSecureSession() {
    // Prevent session fixation attacks
    if (session_status() === PHP_SESSION_NONE) {

        // Set session cookie parameters
        $cookieParams = [
            'lifetime' => 0, // Session cookie (expires when browser closes)
            'path' => '/',
            'domain' => parse_url(SITE_URL, PHP_URL_HOST) ?? '',
            'secure' => SESSION_SECURE, // Only send over HTTPS
            'httponly' => SESSION_HTTPONLY, // Not accessible via JavaScript
            'samesite' => SESSION_SAMESITE // CSRF protection
        ];

        session_set_cookie_params($cookieParams);

        // Additional security settings
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', SESSION_SECURE ? '1' : '0');

        // Start session
        session_start();

        // Regenerate session ID periodically to prevent session fixation
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            // Regenerate session ID every 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }

        // Store user agent to detect session hijacking
        if (!isset($_SESSION['user_agent'])) {
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        } else {
            // Verify user agent hasn't changed
            if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
                // Possible session hijacking - destroy session
                session_unset();
                session_destroy();
                session_start();
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            }
        }

        // Store IP address (optional - can cause issues with mobile users)
        if (!isset($_SESSION['ip_address'])) {
            $_SESSION['ip_address'] = getClientIP();
        }
    }
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    // Validate IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    return $_SERVER['REMOTE_ADDR'] ?? '';
}

/**
 * Destroy session securely
 */
function destroySession() {
    $_SESSION = [];

    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
