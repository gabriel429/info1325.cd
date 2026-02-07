<?php
/**
 * Rate Limiting System
 * Protect against brute force attacks
 */

/**
 * Check if an action should be rate limited
 * @param string $action Action identifier (e.g., 'login', 'password_reset')
 * @param string $identifier User identifier (IP, email, user_id)
 * @param int $max_attempts Maximum attempts allowed
 * @param int $window_seconds Time window in seconds
 * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int]
 */
function rate_limit_check($action, $identifier, $max_attempts = 5, $window_seconds = 900) {
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $key = 'rate_limit_' . $action . '_' . md5($identifier);
    $now = time();

    // Get existing data
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => [],
            'blocked_until' => 0
        ];
    }

    $data = $_SESSION[$key];

    // Check if currently blocked
    if ($data['blocked_until'] > $now) {
        return [
            'allowed' => false,
            'remaining' => 0,
            'reset_time' => $data['blocked_until'],
            'blocked' => true
        ];
    }

    // Clean old attempts outside the window
    $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($now, $window_seconds) {
        return ($now - $timestamp) < $window_seconds;
    });

    // Count current attempts
    $attempt_count = count($data['attempts']);

    // Check if limit exceeded
    if ($attempt_count >= $max_attempts) {
        // Block for the window duration
        $data['blocked_until'] = $now + $window_seconds;
        $_SESSION[$key] = $data;

        return [
            'allowed' => false,
            'remaining' => 0,
            'reset_time' => $data['blocked_until'],
            'blocked' => true
        ];
    }

    // Save data
    $_SESSION[$key] = $data;

    return [
        'allowed' => true,
        'remaining' => $max_attempts - $attempt_count,
        'reset_time' => $now + $window_seconds,
        'blocked' => false
    ];
}

/**
 * Record an attempt (failed or successful)
 * @param string $action Action identifier
 * @param string $identifier User identifier
 */
function rate_limit_record($action, $identifier) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $key = 'rate_limit_' . $action . '_' . md5($identifier);
    $now = time();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => [],
            'blocked_until' => 0
        ];
    }

    $data = $_SESSION[$key];
    $data['attempts'][] = $now;
    $_SESSION[$key] = $data;
}

/**
 * Reset/clear rate limit for an identifier
 * @param string $action Action identifier
 * @param string $identifier User identifier
 */
function rate_limit_reset($action, $identifier) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $key = 'rate_limit_' . $action . '_' . md5($identifier);
    unset($_SESSION[$key]);
}

/**
 * Get client IP address (handles proxies)
 * @return string IP address
 */
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Check for proxy headers (only if you trust your proxy)
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    return $ip;
}

/**
 * Format time remaining for user display
 * @param int $seconds Seconds remaining
 * @return string Formatted time (e.g., "5 minutes")
 */
function format_time_remaining($seconds) {
    if ($seconds < 60) {
        return $seconds . ' seconde' . ($seconds > 1 ? 's' : '');
    } elseif ($seconds < 3600) {
        $minutes = ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } else {
        $hours = ceil($seconds / 3600);
        return $hours . ' heure' . ($hours > 1 ? 's' : '');
    }
}
