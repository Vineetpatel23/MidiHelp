<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($doctor_id > 0) {
    // Delete doctor from database (Foreign Key ON DELETE CASCADE cleans up related appointments)
    $stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);

    if ($stmt->execute()) {
        $_SESSION['flash_success'] = "Doctor record deleted successfully.";
    } else {
        $_SESSION['flash_error'] = "Failed to delete doctor. Database error: " . $conn->error;
    }
    $stmt->close();
}

header("Location: doctors.php");
exit();
?>
