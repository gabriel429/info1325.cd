<?php
/**
 * CSRF Protection System
 * Cross-Site Request Forgery token generation and validation
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate a new CSRF token for the current session
 * @return string The generated token
 */
function csrf_generate_token() {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        // Generate new token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    } else {
        // Check if token is older than 2 hours - regenerate if needed
        if (time() - $_SESSION['csrf_token_time'] > 7200) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
    }

    return $_SESSION['csrf_token'];
}

/**
 * Get the current CSRF token (generates if not exists)
 * @return string The CSRF token
 */
function csrf_token() {
    return csrf_generate_token();
}

/**
 * Output a hidden input field with CSRF token
 */
function csrf_field() {
    $token = csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Validate CSRF token from request
 * @param bool $exit_on_failure Whether to exit script on validation failure
 * @return bool True if valid, false otherwise
 */
function csrf_validate($exit_on_failure = true) {
    // Get token from POST or GET
    $request_token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';

    // Get session token
    $session_token = $_SESSION['csrf_token'] ?? '';

    // Validate
    $is_valid = hash_equals($session_token, $request_token);

    if (!$is_valid && $exit_on_failure) {
        // CSRF validation failed - terminate request
        http_response_code(403);
        die('CSRF validation failed. Request rejected for security reasons.');
    }

    return $is_valid;
}

/**
 * Check if request is a POST with CSRF validation
 * Commonly used pattern in forms
 * @return bool True if POST request with valid CSRF token
 */
function is_post_with_csrf() {
    return $_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate(false);
}

/**
 * Require valid CSRF token for POST requests
 * Call this at the beginning of scripts that process forms
 */
function csrf_protect_post() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_validate(true);
    }
}

/**
 * Generate CSRF meta tag for AJAX requests
 */
function csrf_meta_tag() {
    $token = csrf_token();
    echo '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
