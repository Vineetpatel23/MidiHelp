<?php
// ========================================================
// MidiHelp - Admin Authentication & Session Guard
// ========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get current admin session info
 */
function get_admin_user() {
    if (is_admin_logged_in()) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? 'Admin'
        ];
    }
    return null;
}

/**
 * Enforce admin authentication guard
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        $_SESSION['flash_error'] = "Admin authentication required. Please log in first.";
        header("Location: login.php");
        exit();
    }
}
?>
