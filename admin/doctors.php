<?php
$page_title = "Manage Doctors - Admin Portal";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

// Fetch all doctors from database
$query = "SELECT * FROM doctors ORDER BY id DESC";
$result = $conn->query($query);
$doctors = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - Admin Portal</title>
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
            <a href="doctors.php" class="admin-menu-link active">🩺 <span>Manage Doctors</span></a>
            <a href="patients.php" class="admin-menu-link">👥 <span>Manage Patients</span></a>
            <a href="appointments.php" class="admin-menu-link">📅 <span>Appointments</span></a>
            <a href="../index.php" target="_blank" class="admin-menu-link">🌐 <span>Live Website</span></a>
            <a href="logout.php" class="admin-menu-link" style="margin-top: auto; color: #FCA5A5;">🚪 <span>Logout</span></a>
        </div>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h1 class="admin-title">Manage Medical Doctors</h1>
                <p style="color: var(--muted-text); font-size: 0.9rem; margin-top: 2px;">Add, edit, or remove doctors from the MidiHelp directory.</p>
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

        <div style="background: white; border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor Name</th>
                            <th>Specialization</th>
                            <th>Experience</th>
                            <th>Fee</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($doctors)): ?>
                            <?php foreach ($doctors as $doc): ?>
                                <tr>
                                    <td><strong>#DOC-<?php echo sprintf("%03d", $doc['id']); ?></strong></td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark-text);"><?php echo htmlspecialchars($doc['name']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--muted-text);"><?php echo htmlspecialchars($doc['email']); ?></div>
                                    </td>
                                    <td><span style="color: var(--primary); font-weight: 600;"><?php echo htmlspecialchars($doc['specialization']); ?></span></td>
                                    <td><?php echo htmlspecialchars($doc['experience']); ?></td>
                                    <td style="font-weight: 700; color: var(--primary);">₹<?php echo number_format($doc['consultation_fee'], 2); ?></td>
                                    <td style="font-size: 0.85rem;">
                                        <div><?php echo htmlspecialchars($doc['available_days']); ?></div>
                                        <div style="color: var(--muted-text);"><?php echo htmlspecialchars($doc['available_time']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($doc['status'] == 'Available') ? 'badge-confirmed' : 'badge-cancelled'; ?>">
                                            <?php echo htmlspecialchars($doc['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="edit-doctor.php?id=<?php echo $doc['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                            <a href="delete-doctor.php?id=<?php echo $doc['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete('Are you sure you want to delete <?php echo htmlspecialchars($doc['name']); ?>? This action will remove all doctor records.')">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 30px; color: var(--muted-text);">No doctors found in directory. Click "+ Add New Doctor" to register one.</td>
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
