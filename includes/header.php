<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$site_title = isset($page_title) ? $page_title . " - MidiHelp" : "MidiHelp - Doctor Appointment System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <!-- Custom CSS Stylesheets -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
<?php include_once __DIR__ . '/navbar.php'; ?>
<main class="main-content">
<?php
// Display flash success or error alerts if set in session
if (isset($_SESSION['flash_success'])) {
    echo '<div class="container"><div class="alert alert-success"><span>' . htmlspecialchars($_SESSION['flash_success']) . '</span><button class="alert-close">✕</button></div></div>';
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo '<div class="container"><div class="alert alert-danger"><span>' . htmlspecialchars($_SESSION['flash_error']) . '</span><button class="alert-close">✕</button></div></div>';
    unset($_SESSION['flash_error']);
}
?>
