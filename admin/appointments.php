<?php
$page_title = "Manage Appointments - Admin Portal";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

// Filter status from GET query
$filter_status = isset($_GET['status']) ? clean_input($_GET['status'], $conn) : '';

$query = "SELECT a.*, d.name AS doctor_name, d.specialization 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id WHERE 1=1";

if (!empty($filter_status)) {
    $query .= " AND a.status = '" . $conn->real_escape_string($filter_status) . "'";
}

$query .= " ORDER BY a.id DESC";
$result = $conn->query($query);
$appointments = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin Portal</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="logo-icon" style="width: 32px; height: 32px; font-size: 1rem;">+</div>
            <span>MidiHelp Admin</span>
        </div>
        <div class="admin-menu">
            <a href="dashboard.php" class="admin-menu-link">📊 <span>Dashboard</span></a>
            <a href="doctors.php" class="admin-menu-link">🩺 <span>Manage Doctors</span></a>
            <a href="patients.php" class="admin-menu-link">👥 <span>Manage Patients</span></a>
            <a href="appointments.php" class="admin-menu-link active">📅 <span>Appointments</span></a>
            <a href="../index.php" target="_blank" class="admin-menu-link">🌐 <span>Live Website</span></a>
            <a href="logout.php" class="admin-menu-link" style="margin-top: auto; color: #FCA5A5;">🚪 <span>Logout</span></a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h1 class="admin-title">All System Appointments</h1>
                <p style="color: var(--muted-text); font-size: 0.9rem; margin-top: 2px;">Review patient bookings and change appointment statuses.</p>
            </div>
        </div>

        <?php
        if (isset($_SESSION['flash_success'])) {
            echo '<div class="alert alert-success"><span>' . htmlspecialchars($_SESSION['flash_success']) . '</span><button class="alert-close">✕</button></div>';
            unset($_SESSION['flash_success']);
        }
        if (isset($_SESSION['flash_error'])) {
            echo '<div class="alert alert-danger"><span>' . htmlspecialchars($_SESSION['flash_error']) . '</span><button class="alert-close">✕</button></div>';
            unset($_SESSION['flash_error']);
        }
        ?>

        <!-- FILTER TABS -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="appointments.php" class="btn btn-sm <?php echo empty($filter_status) ? 'btn-primary' : 'btn-outline'; ?>">All Appointments</a>
            <a href="appointments.php?status=Pending" class="btn btn-sm <?php echo ($filter_status === 'Pending') ? 'btn-primary' : 'btn-outline'; ?>">Pending</a>
            <a href="appointments.php?status=Confirmed" class="btn btn-sm <?php echo ($filter_status === 'Confirmed') ? 'btn-primary' : 'btn-outline'; ?>">Confirmed</a>
            <a href="appointments.php?status=Completed" class="btn btn-sm <?php echo ($filter_status === 'Completed') ? 'btn-primary' : 'btn-outline'; ?>">Completed</a>
            <a href="appointments.php?status=Cancelled" class="btn btn-sm <?php echo ($filter_status === 'Cancelled') ? 'btn-primary' : 'btn-outline'; ?>">Cancelled</a>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>APT ID</th>
                            <th>Patient Information</th>
                            <th>Doctor & Specialty</th>
                            <th>Date & Time</th>
                            <th>Symptoms / Concern</th>
                            <th>Current Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $apt): ?>
                                <tr>
                                    <td><strong>#APT-<?php echo sprintf("%04d", $apt['id']); ?></strong></td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($apt['patient_name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);"><?php echo htmlspecialchars($apt['patient_email']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);"><?php echo htmlspecialchars($apt['patient_phone']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($apt['doctor_name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($apt['specialization']); ?></div>
                                    </td>
                                    <td>
                                        <div>📅 <?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);">⏰ <?php echo htmlspecialchars($apt['appointment_time']); ?></div>
                                    </td>
                                    <td style="max-width: 220px; font-size: 0.85rem; color: var(--dark-text);">
                                        <?php echo !empty($apt['symptoms']) ? htmlspecialchars($apt['symptoms']) : '<em style="color: var(--muted-text);">None specified</em>'; ?>
                                    </td>
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
                                        <form action="update-appointment.php" method="POST" style="display: flex; gap: 6px;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $apt['id']; ?>">
                                            <select name="status" class="form-control" style="padding: 6px 10px; font-size: 0.85rem; height: auto;" onchange="this.form.submit()">
                                                <option value="Pending" <?php echo ($apt['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Confirmed" <?php echo ($apt['status'] === 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="Completed" <?php echo ($apt['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                <option value="Cancelled" <?php echo ($apt['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: var(--muted-text);">No appointments found for this filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="../js/main.js"></script>
</body>
</html>
