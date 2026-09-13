<?php
$page_title = "Appointment Booked Successfully - MidiHelp";
require_once __DIR__ . '/config/database.php';

$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($appointment_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch appointment details joined with doctor info
$query = "SELECT a.*, d.name AS doctor_name, d.specialization, d.consultation_fee, d.phone AS doctor_phone 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id 
          WHERE a.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$appointment = $result->fetch_assoc();
$stmt->close();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div style="max-width: 650px; margin: 0 auto; text-align: center;">
        
        <!-- SUCCESS ICON BADGE -->
        <div style="width: 80px; height: 80px; background: var(--success-bg); color: var(--success); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px auto; border: 2px solid #BBF7D0;">
            ✓
        </div>

        <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--dark-text); margin-bottom: 8px;">Appointment Booked Successfully!</h1>
        <p style="color: var(--muted-text); font-size: 1rem; margin-bottom: 30px;">
            Your appointment has been registered with ID <strong>#APT-<?php echo sprintf("%04d", $appointment['id']); ?></strong>.
        </p>

        <!-- SUMMARY CARD -->
        <div style="background: var(--card-bg); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid var(--border-color); text-align: left; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Appointment ID</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: var(--primary);">#APT-<?php echo sprintf("%04d", $appointment['id']); ?></div>
                </div>
                <span class="badge badge-pending" style="font-size: 0.85rem; padding: 6px 14px;">
                    Status: <?php echo htmlspecialchars($appointment['status']); ?>
                </span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Doctor Name</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: var(--dark-text);"><?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                    <div style="font-size: 0.85rem; color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($appointment['specialization']); ?></div>
                </div>

                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Consultation Fee</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: var(--primary);">₹<?php echo number_format($appointment['consultation_fee'], 2); ?></div>
                </div>

                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Date & Time</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--dark-text);">
                        📅 <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                    </div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--dark-text);">
                        ⏰ <?php echo htmlspecialchars($appointment['appointment_time']); ?>
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Patient Name</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($appointment['patient_name']); ?></div>
                    <div style="font-size: 0.85rem; color: var(--muted-text);"><?php echo htmlspecialchars($appointment['patient_phone']); ?></div>
                </div>
            </div>

            <?php if (!empty($appointment['symptoms'])): ?>
                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Symptoms / Concern</div>
                    <div style="font-size: 0.9rem; color: var(--dark-text); margin-top: 4px;"><?php echo nl2br(htmlspecialchars($appointment['symptoms'])); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 16px; justify-content: center;">
            <?php if (is_logged_in()): ?>
                <a href="appointments.php" class="btn btn-primary btn-lg">View My Appointments</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary btn-lg">Login to View Appointments</a>
            <?php endif; ?>
            <a href="index.php" class="btn btn-outline btn-lg">Back to Home</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
