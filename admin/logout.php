<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

$_SESSION['flash_success'] = "Logged out from admin panel.";
header("Location: login.php");
exit();
?>
