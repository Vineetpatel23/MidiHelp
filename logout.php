<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset patient session variables
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);

$_SESSION['flash_success'] = "You have been logged out successfully.";
header("Location: index.php");
exit();
?>
