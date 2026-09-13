<?php
$page_title = "Edit Doctor - Admin Portal";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

require_admin_login();

$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($doctor_id <= 0) {
    header("Location: doctors.php");
    exit();
}

// Fetch doctor details
$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $_SESSION['flash_error'] = "Doctor not found.";
    header("Location: doctors.php");
    exit();
}

$doctor = $res->fetch_assoc();
$stmt->close();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'], $conn);
    $specialization = clean_input($_POST['specialization'], $conn);
    $qualification = clean_input($_POST['qualification'], $conn);
    $experience = clean_input($_POST['experience'], $conn);
    $phone = clean_input($_POST['phone'], $conn);
    $email = clean_input($_POST['email'], $conn);
    $consultation_fee = floatval($_POST['consultation_fee']);
    $available_days = clean_input($_POST['available_days'], $conn);
    $available_time = clean_input($_POST['available_time'], $conn);
    $description = clean_input($_POST['description'], $conn);
    $status = clean_input($_POST['status'], $conn);

    if (empty($name)) $errors[] = "Doctor name is required.";
    if (empty($specialization)) $errors[] = "Specialization is required.";
    if (empty($qualification)) $errors[] = "Qualification is required.";

    if (empty($errors)) {
        $u_stmt = $conn->prepare("UPDATE doctors SET name=?, specialization=?, qualification=?, experience=?, phone=?, email=?, consultation_fee=?, available_days=?, available_time=?, description=?, status=? WHERE id=?");
        $u_stmt->bind_param("ssssssdssssi", $name, $specialization, $qualification, $experience, $phone, $email, $consultation_fee, $available_days, $available_time, $description, $status, $doctor_id);

        if ($u_stmt->execute()) {
            $u_stmt->close();
            $_SESSION['flash_success'] = "Doctor record updated successfully!";
            header("Location: doctors.php");
            exit();
        } else {
            $errors[] = "Update failed. Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor - Admin Portal</title>
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
                <h1 class="admin-title">Edit Doctor Information</h1>
                <p style="color: var(--muted-text); font-size: 0.9rem; margin-top: 2px;">Updating profile for <?php echo htmlspecialchars($doctor['name']); ?></p>
            </div>
            <a href="doctors.php" class="btn btn-outline">← Back to Doctors List</a>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div>
                    <?php foreach ($errors as $err): ?>
                        <div>• <?php echo htmlspecialchars($err); ?></div>
                    <?php endforeach; ?>
                </div>
                <button class="alert-close">✕</button>
            </div>
        <?php endif; ?>

        <div style="background: white; border-radius: var(--radius-lg); padding: 30px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); max-width: 800px;">
            <form action="edit-doctor.php?id=<?php echo $doctor_id; ?>" method="POST">
                <div class="form-row" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label for="name" class="form-label">Doctor Name *</label>
                        <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($doctor['name']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="specialization" class="form-label">Specialization *</label>
                        <select id="specialization" name="specialization" class="form-control" required>
                            <?php
                            $specs = ["General Physician", "Cardiologist", "Dermatologist", "Dentist", "Pediatrician", "Orthopedic", "Neurologist", "Gynecologist", "Radiologist"];
                            foreach ($specs as $sp) {
                                $selected = ($doctor['specialization'] === $sp) ? 'selected' : '';
                                echo '<option value="' . $sp . '" ' . $selected . '>' . $sp . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label for="qualification" class="form-label">Qualification *</label>
                        <input type="text" id="qualification" name="qualification" class="form-control" required value="<?php echo htmlspecialchars($doctor['qualification']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="experience" class="form-label">Experience *</label>
                        <input type="text" id="experience" name="experience" class="form-control" required value="<?php echo htmlspecialchars($doctor['experience']); ?>">
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required value="<?php echo htmlspecialchars($doctor['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($doctor['email']); ?>">
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label for="consultation_fee" class="form-label">Consultation Fee (₹) *</label>
                        <input type="number" step="0.01" id="consultation_fee" name="consultation_fee" class="form-control" required value="<?php echo htmlspecialchars($doctor['consultation_fee']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">Availability Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="Available" <?php echo ($doctor['status'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Unavailable" <?php echo ($doctor['status'] === 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 18px;">
                    <div class="form-group">
                        <label for="available_days" class="form-label">Available Days</label>
                        <input type="text" id="available_days" name="available_days" class="form-control" required value="<?php echo htmlspecialchars($doctor['available_days']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="available_time" class="form-label">Available Time</label>
                        <input type="text" id="available_time" name="available_time" class="form-control" required value="<?php echo htmlspecialchars($doctor['available_time']); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="description" class="form-label">Doctor Bio / Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($doctor['description'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Update Doctor Record</button>
            </form>
        </div>
    </main>
</div>

<script src="../js/main.js"></script>
</body>
</html>
