<?php
/**
 * Centralized Authentication & Authorization Check
 * Include this file at the top of protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    // Not authenticated - redirect to login
    require_once __DIR__ . '/../configUrl.php';
    require_once __DIR__ . '/../defConstLiens.php';
    header('Location: ' . URL_AUTHENTIFICATION);
    exit;
}

// Check if account is active (for users with active status)
if (isset($_SESSION['active']) && !$_SESSION['active']) {
    // Account was deactivated - force logout
    require_once __DIR__ . '/../configUrl.php';
    require_once __DIR__ . '/../defConstLiens.php';
    session_destroy();
    header('Location: ' . URL_AUTHENTIFICATION . '?error=account_disabled');
    exit;
}

/**
 * Require specific role(s) to access the page
 * @param string|array $required_roles Single role or array of allowed roles
 * @param string $redirect_url Where to redirect if access denied (optional)
 */
function require_role($required_roles, $redirect_url = null) {
    $user_role = $_SESSION['role'] ?? '';

    // Convert single role to array for uniform handling
    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }

    // Check if user has one of the required roles
    if (!in_array($user_role, $required_roles, true)) {
        // Access denied
        if ($redirect_url) {
            header('Location: ' . $redirect_url);
        } else {
            require_once __DIR__ . '/../configUrl.php';
            require_once __DIR__ . '/../defConstLiens.php';
            header('Location: ' . URL_AUTHENTIFICATION);
        }
        exit;
    }
}

/**
 * Check if user has a specific role
 * @param string $role Role to check
 * @return bool
 */
function has_role($role) {
    return ($_SESSION['role'] ?? '') === $role;
}

/**
 * Check if user is admin
 * @return bool
 */
function is_admin() {
    return has_role('admin');
}
