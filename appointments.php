<?php
$page_title = "My Appointments - Patient Dashboard";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce login guard
require_login();

$user = get_logged_user();
$user_id = $user['id'];

// Fetch patient appointment statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$stat_stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM appointments WHERE user_id = ? GROUP BY status");
$stat_stmt->bind_param("i", $user_id);
$stat_stmt->execute();
$stat_res = $stat_stmt->get_result();
while ($s_row = $stat_res->fetch_assoc()) {
    $st = strtolower($s_row['status']);
    if (isset($stats[$st])) {
        $stats[$st] = $s_row['count'];
    }
    $stats['total'] += $s_row['count'];
}
$stat_stmt->close();

// Fetch all appointment records for this patient
$query = "SELECT a.*, d.name AS doctor_name, d.specialization, d.consultation_fee 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id 
          WHERE a.user_id = ? 
          ORDER BY a.appointment_date DESC, a.id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$stmt->close();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 20px; padding-bottom: 60px;">
    <!-- DASHBOARD HEADER -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--dark-text); margin-bottom: 4px;">My Appointments Dashboard</h1>
            <p style="color: var(--muted-text); font-size: 0.95rem;">Welcome back, <strong><?php echo htmlspecialchars($user['name']); ?></strong>! Here is your appointment history.</p>
        </div>
        <a href="book-appointment.php" class="btn btn-primary btn-lg">+ Book New Appointment</a>
    </div>

    <!-- STATS METRIC CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 35px;">
        <div style="background: var(--card-bg); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--muted-text); text-transform: uppercase;">Total</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--dark-text); margin-top: 4px;"><?php echo $stats['total']; ?></div>
        </div>

        <div style="background: var(--card-bg); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--warning); text-transform: uppercase;">Pending</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--warning); margin-top: 4px;"><?php echo $stats['pending']; ?></div>
        </div>

        <div style="background: var(--card-bg); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--success); text-transform: uppercase;">Confirmed</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--success); margin-top: 4px;"><?php echo $stats['confirmed']; ?></div>
        </div>

        <div style="background: var(--card-bg); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--info); text-transform: uppercase;">Completed</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--info); margin-top: 4px;"><?php echo $stats['completed']; ?></div>
        </div>

        <div style="background: var(--card-bg); padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); text-align: center;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--danger); text-transform: uppercase;">Cancelled</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger); margin-top: 4px;"><?php echo $stats['cancelled']; ?></div>
        </div>
    </div>

    <!-- APPOINTMENTS HISTORY TABLE -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>APT ID</th>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Date & Time</th>
                    <th>Patient Name</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($appointments)): ?>
                    <?php foreach ($appointments as $apt): ?>
                        <tr>
                            <td><strong>#APT-<?php echo sprintf("%04d", $apt['id']); ?></strong></td>
                            <td style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($apt['doctor_name']); ?></td>
                            <td><span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($apt['specialization']); ?></span></td>
                            <td>
                                <div>📅 <?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?></div>
                                <div style="font-size: 0.8rem; color: var(--muted-text);">⏰ <?php echo htmlspecialchars($apt['appointment_time']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                            <td style="font-weight: 700;">₹<?php echo number_format($apt['consultation_fee'], 2); ?></td>
                            <td>
                                <?php
                                $badge_class = 'badge-pending';
                                if ($apt['status'] === 'Confirmed') $badge_class = 'badge-confirmed';
                                elseif ($apt['status'] === 'Completed') $badge_class = 'badge-completed';
                                elseif ($apt['status'] === 'Cancelled') $badge_class = 'badge-cancelled';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($apt['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="appointment-success.php?id=<?php echo $apt['id']; ?>" class="btn btn-outline btn-sm">View Receipt</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--muted-text);">
                            No appointments found. Click "+ Book New Appointment" to schedule your first consultation.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
