<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $status = isset($_POST['status']) ? clean_input($_POST['status'], $conn) : '';

    $allowed_statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    if ($appointment_id > 0 && in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $appointment_id);

        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Appointment #APT-" . sprintf("%04d", $appointment_id) . " status updated to '{$status}'.";
        } else {
            $_SESSION['flash_error'] = "Failed to update status. Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['flash_error'] = "Invalid status update request.";
    }
}

$redirect = $_SERVER['HTTP_REFERER'] ?? 'appointments.php';
header("Location: " . $redirect);
exit();
?>
