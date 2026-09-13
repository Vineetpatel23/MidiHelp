<?php
// ========================================================
// MidiHelp - AJAX Endpoint for Booked Time Slots
// Returns JSON array of non-cancelled booked slots for doctor & date
// ========================================================

header('Content-Type: application/json');
require_once __DIR__ . '/config/database.php';

$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
$date = isset($_GET['date']) ? trim($_GET['date']) : '';

$response = [
    'success' => false,
    'booked_slots' => []
];

if ($doctor_id > 0 && !empty($date)) {
    // Select booked slots where status is not 'Cancelled'
    $stmt = $conn->prepare("SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND status != 'Cancelled'");
    if ($stmt) {
        $stmt->bind_param("is", $doctor_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response['booked_slots'][] = $row['appointment_time'];
        }
        $stmt->close();
        $response['success'] = true;
    }
}

echo json_encode($response);
exit();
?>
