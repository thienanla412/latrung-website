<?php
/**
 * CSRF Protection System
 * Generates and validates CSRF tokens for forms
 */

require_once __DIR__ . '/session.php';

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    initSecureSession();

    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) ||
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    initSecureSession();

    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }

    // Check if token has expired
    if ((time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }

    // Use hash_equals to prevent timing attacks
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token field HTML
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Get CSRF token for AJAX requests
 */
function getCSRFToken() {
    return generateCSRFToken();
}

/**
 * Verify CSRF token from request
 */
function verifyCSRF() {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (!validateCSRFToken($token)) {
        http_response_code(403);
        logSecurityEvent('CSRF token validation failed', [
            'ip' => getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? ''
        ]);

        if (APP_DEBUG) {
            die('CSRF token validation failed. Please refresh the page and try again.');
        } else {
            die('Security validation failed. Please refresh the page and try again.');
        }
    }

    return true;
}

/**
 * Log security events
 */
function logSecurityEvent($message, $context = []) {
    if (!LOG_ENABLED) {
        return;
    }

    $logFile = LOG_PATH . 'security.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextString = !empty($context) ? json_encode($context) : '';
    $logMessage = "[{$timestamp}] {$message} {$contextString}\n";

    // Create log directory if it doesn't exist
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }

    error_log($logMessage, 3, $logFile);
}
