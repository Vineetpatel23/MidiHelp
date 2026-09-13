<?php
// ========================================================
// MidiHelp - Patient Authentication & Session Guard
// ========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a patient user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user details array
 */
function get_logged_user() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'Patient',
            'email' => $_SESSION['user_email'] ?? ''
        ];
    }
    return null;
}

/**
 * Enforce authentication on patient-only pages
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = "Please log in to your patient account to access this page.";
        $redirect_to = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?redirect=" . $redirect_to);
        exit();
    }
}
?>
