<?php

// Include database configuration
require_once 'config.php';

/**
 * Checks if a user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Checks if the current user has a specific role
 * @param string $role The role to check (admin, artist, buyer)
 * @return bool True if user has the role, false otherwise
 */
function has_role($role) {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirects to login page if not logged in
 * @param string $redirect_url URL to redirect to after login
 */
function require_login($redirect_url = '') {
    if (!is_logged_in()) {
        $login_url = SITE_URL . '/login.php';
        if (!empty($redirect_url)) {
            $login_url .= '?redirect=' . urlencode($redirect_url);
        }
        header("Location: " . $login_url);
        exit();
    }
}

/**
 * Redirects if user doesn't have required role
 * @param string $role Required role
 * @param string $redirect_url URL to redirect to if unauthorized
 */
function require_role($role, $redirect_url = '') {
    require_login($redirect_url);
    if (!has_role($role)) {
        if (empty($redirect_url)) {
            $redirect_url = SITE_URL . '/unauthorized.php';
        }
        header("Location: " . $redirect_url);
        exit();
    }
}

/**
 * Gets current user's ID
 * @return int|null User ID or null if not logged in
 */
function get_user_id() {
    return is_logged_in() ? $_SESSION['user_id'] : null;
}

/**
 * Gets current user's role
 * @return string|null User role or null if not logged in
 */
function get_user_role() {
    return is_logged_in() ? $_SESSION['role'] : null;
}

/**
 * Verifies CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generates and stores a new CSRF token
 * @return string The generated token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Gets the current user's data from database
 * @return array|null User data or null if not found
 */
function get_current_user_data() {
    if (!is_logged_in()) {
        return null;
    }

    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching user data: " . $e->getMessage());
        return null;
    }
}

/**
 * Checks if user is an artist and gets artist data
 * @return array|null Artist data or null if not an artist
 */
function get_current_artist_data() {
    if (!has_role('artist')) {
        return null;
    }

    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM artists WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching artist data: " . $e->getMessage());
        return null;
    }
}

/**
 * Regenerates session ID and updates session data
 */
function regenerate_session() {
    session_regenerate_id(true);
    $_SESSION['last_activity'] = time();
}

// Auto-regenerate session ID every 30 minutes for security
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    regenerate_session();
}

// Initialize CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    generate_csrf_token();
}

// Check for session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: " . SITE_URL . "/login.php?timeout=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
