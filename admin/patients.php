<?php
$page_title = "Manage Patients - Admin Portal";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

// Fetch all registered patients
$query = "SELECT * FROM users ORDER BY id DESC";
$result = $conn->query($query);
$patients = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - Admin Portal</title>
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
            <a href="patients.php" class="admin-menu-link active">👥 <span>Manage Patients</span></a>
            <a href="appointments.php" class="admin-menu-link">📅 <span>Appointments</span></a>
            <a href="../index.php" target="_blank" class="admin-menu-link">🌐 <span>Live Website</span></a>
            <a href="logout.php" class="admin-menu-link" style="margin-top: auto; color: #FCA5A5;">🚪 <span>Logout</span></a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h1 class="admin-title">Registered Patient Directory</h1>
                <p style="color: var(--muted-text); font-size: 0.9rem; margin-top: 2px;">View registered patients and user account information.</p>
            </div>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Address</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($patients)): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><strong>#PAT-<?php echo sprintf("%04d", $patient['id']); ?></strong></td>
                                    <td style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($patient['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                    <td><?php echo !empty($patient['date_of_birth']) ? date('M j, Y', strtotime($patient['date_of_birth'])) : 'N/A'; ?></td>
                                    <td style="max-width: 200px; font-size: 0.85rem; color: var(--muted-text);"><?php echo htmlspecialchars($patient['address'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($patient['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: var(--muted-text);">No registered patients found.</td>
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
