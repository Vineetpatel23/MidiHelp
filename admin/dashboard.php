<?php
$page_title = "Admin Dashboard - MidiHelp";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

// Enforce admin login guard
require_admin_login();

$admin = get_admin_user();

// Fetch System Analytics / Metrics
$total_doctors = 0;
$total_patients = 0;
$stats = [
    'total' => 0,
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

// Count Doctors
$doc_res = $conn->query("SELECT COUNT(*) as count FROM doctors");
if ($doc_res) {
    $total_doctors = $doc_res->fetch_assoc()['count'];
}

// Count Patients
$pat_res = $conn->query("SELECT COUNT(*) as count FROM users");
if ($pat_res) {
    $total_patients = $pat_res->fetch_assoc()['count'];
}

// Count Appointments by status
$apt_res = $conn->query("SELECT status, COUNT(*) as count FROM appointments GROUP BY status");
if ($apt_res) {
    while ($r = $apt_res->fetch_assoc()) {
        $st = strtolower($r['status']);
        if (isset($stats[$st])) {
            $stats[$st] = $r['count'];
        }
        $stats['total'] += $r['count'];
    }
}

// Fetch recent 10 appointments
$query = "SELECT a.*, d.name AS doctor_name, d.specialization 
          FROM appointments a 
          JOIN doctors d ON a.doctor_id = d.id 
          ORDER BY a.id DESC LIMIT 10";
$recent_appointments = [];
$res = $conn->query($query);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $recent_appointments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MidiHelp</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <!-- ADMIN SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <div class="logo-icon" style="width: 32px; height: 32px; font-size: 1rem;">+</div>
            <span>MidiHelp Admin</span>
        </div>

        <div class="admin-menu">
            <a href="dashboard.php" class="admin-menu-link active">📊 <span>Dashboard</span></a>
            <a href="doctors.php" class="admin-menu-link">🩺 <span>Manage Doctors</span></a>
            <a href="patients.php" class="admin-menu-link">👥 <span>Manage Patients</span></a>
            <a href="appointments.php" class="admin-menu-link">📅 <span>Appointments</span></a>
            <a href="../index.php" target="_blank" class="admin-menu-link">🌐 <span>Live Website</span></a>
            <a href="logout.php" class="admin-menu-link" style="margin-top: auto; color: #FCA5A5;">🚪 <span>Logout</span></a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h1 class="admin-title">System Overview</h1>
                <p style="color: var(--muted-text); font-size: 0.9rem; margin-top: 2px;">Welcome back, <strong><?php echo htmlspecialchars($admin['username']); ?></strong>!</p>
            </div>
            <a href="add-doctor.php" class="btn btn-primary">+ Add New Doctor</a>
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

        <!-- ANALYTICS METRIC CARDS -->
        <div class="admin-metrics-grid">
            <div class="metric-card">
                <div>
                    <div class="metric-label">Total Doctors</div>
                    <div class="metric-number"><?php echo $total_doctors; ?></div>
                </div>
                <div class="metric-icon icon-teal">🩺</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Total Patients</div>
                    <div class="metric-number"><?php echo $total_patients; ?></div>
                </div>
                <div class="metric-icon icon-blue">👥</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Total Appointments</div>
                    <div class="metric-number"><?php echo $stats['total']; ?></div>
                </div>
                <div class="metric-icon icon-teal">📅</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Pending</div>
                    <div class="metric-number" style="color: var(--warning);"><?php echo $stats['pending']; ?></div>
                </div>
                <div class="metric-icon icon-yellow">⏳</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Confirmed</div>
                    <div class="metric-number" style="color: var(--success);"><?php echo $stats['confirmed']; ?></div>
                </div>
                <div class="metric-icon icon-green">✓</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Completed</div>
                    <div class="metric-number" style="color: var(--info);"><?php echo $stats['completed']; ?></div>
                </div>
                <div class="metric-icon icon-blue">🏁</div>
            </div>

            <div class="metric-card">
                <div>
                    <div class="metric-label">Cancelled</div>
                    <div class="metric-number" style="color: var(--danger);"><?php echo $stats['cancelled']; ?></div>
                </div>
                <div class="metric-icon icon-red">✕</div>
            </div>
        </div>

        <!-- RECENT APPOINTMENTS TABLE -->
        <div style="background: white; border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--dark-text); margin: 0;">Recent Appointments</h3>
                <a href="appointments.php" style="font-size: 0.9rem; font-weight: 700;">View All Appointments →</a>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>APT ID</th>
                            <th>Patient Name</th>
                            <th>Doctor Name</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Change Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_appointments)): ?>
                            <?php foreach ($recent_appointments as $apt): ?>
                                <tr>
                                    <td><strong>#APT-<?php echo sprintf("%04d", $apt['id']); ?></strong></td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($apt['patient_name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);"><?php echo htmlspecialchars($apt['patient_phone']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($apt['doctor_name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--primary);"><?php echo htmlspecialchars($apt['specialization']); ?></div>
                                    </td>
                                    <td>
                                        <div>📅 <?php echo date('M j, Y', strtotime($apt['appointment_date'])); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);">⏰ <?php echo htmlspecialchars($apt['appointment_time']); ?></div>
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
                                            <select name="status" class="form-control" style="padding: 4px 8px; font-size: 0.8rem; height: auto;" onchange="this.form.submit()">
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
                                <td colspan="6" style="text-align: center; padding: 30px; color: var(--muted-text);">No recent appointments found.</td>
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
