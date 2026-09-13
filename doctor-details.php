<?php
require_once __DIR__ . '/config/database.php';

// Validate doctor ID from GET
$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($doctor_id <= 0) {
    header("Location: doctors.php");
    exit();
}

// Fetch doctor details
$stmt = $conn->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_error'] = "Doctor profile not found.";
    header("Location: doctors.php");
    exit();
}

$doctor = $result->fetch_assoc();
$stmt->close();

$page_title = $doctor['name'] . " - Doctor Profile";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 20px;">
    <!-- BREADCRUMB -->
    <div style="margin-bottom: 20px; font-size: 0.9rem; color: var(--muted-text);">
        <a href="index.php">Home</a> &nbsp;/&nbsp; <a href="doctors.php">Doctors</a> &nbsp;/&nbsp; <span style="color: var(--dark-text); font-weight: 600;"><?php echo htmlspecialchars($doctor['name']); ?></span>
    </div>

    <!-- DOCTOR HEADER CARD -->
    <div class="doctor-detail-header">
        <div class="detail-avatar-wrapper">
            <div style="width: 100%; height: 100%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 4rem; font-weight: 800;">
                <?php 
                $initials = implode('', array_map(function($w) { return $w[0]; }, explode(' ', str_replace('Dr. ', '', $doctor['name']))));
                echo htmlspecialchars(substr($initials, 0, 2));
                ?>
            </div>
        </div>

        <div>
            <div class="doctor-spec" style="font-size: 0.95rem; margin-bottom: 6px;"><?php echo htmlspecialchars($doctor['specialization']); ?></div>
            <h1 class="detail-info-title"><?php echo htmlspecialchars($doctor['name']); ?></h1>
            <p style="color: var(--muted-text); font-weight: 600; margin-bottom: 16px;">🎓 <?php echo htmlspecialchars($doctor['qualification']); ?></p>

            <span class="badge <?php echo ($doctor['status'] == 'Available') ? 'badge-confirmed' : 'badge-cancelled'; ?>" style="font-size: 0.85rem; padding: 6px 14px;">
                Status: <?php echo htmlspecialchars($doctor['status']); ?>
            </span>

            <div class="detail-meta-list">
                <div>
                    <div class="meta-item-label">Experience</div>
                    <div class="meta-item-value"><?php echo htmlspecialchars($doctor['experience']); ?></div>
                </div>
                <div>
                    <div class="meta-item-label">Consultation Fee</div>
                    <div class="meta-item-value" style="color: var(--primary);">₹<?php echo number_format($doctor['consultation_fee'], 2); ?></div>
                </div>
                <div>
                    <div class="meta-item-label">Available Days</div>
                    <div class="meta-item-value"><?php echo htmlspecialchars($doctor['available_days']); ?></div>
                </div>
                <div>
                    <div class="meta-item-label">Working Hours</div>
                    <div class="meta-item-value"><?php echo htmlspecialchars($doctor['available_time']); ?></div>
                </div>
            </div>

            <div style="display: flex; gap: 14px; margin-top: 20px;">
                <?php if ($doctor['status'] === 'Available'): ?>
                    <a href="book-appointment.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary btn-lg">Book Appointment Now</a>
                <?php else: ?>
                    <button class="btn btn-outline btn-lg" disabled style="opacity: 0.5;">Doctor Currently Unavailable</button>
                <?php endif; ?>
                <a href="doctors.php" class="btn btn-outline btn-lg">Back to Doctors</a>
            </div>
        </div>
    </div>

    <!-- DOCTOR DESCRIPTION & CONTACT DETAILS -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-bottom: 60px;">
        <div style="background: var(--card-bg); padding: 30px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--dark-text); margin-bottom: 16px;">About Doctor</h3>
            <p style="color: var(--muted-text); line-height: 1.8; font-size: 1rem;">
                <?php echo nl2br(htmlspecialchars($doctor['description'] ?? 'No bio description available.')); ?>
            </p>
        </div>

        <div style="background: var(--card-bg); padding: 30px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--dark-text); margin-bottom: 16px;">Contact Information</h3>
            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 0.95rem;">
                <div>
                    <div style="font-weight: 700; color: var(--muted-text); font-size: 0.8rem; text-transform: uppercase;">Direct Line</div>
                    <div style="font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($doctor['phone']); ?></div>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--muted-text); font-size: 0.8rem; text-transform: uppercase;">Email Address</div>
                    <div style="font-weight: 600; color: var(--dark-text);"><?php echo htmlspecialchars($doctor['email']); ?></div>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--muted-text); font-size: 0.8rem; text-transform: uppercase;">Hospital Location</div>
                    <div style="font-weight: 600; color: var(--dark-text);">MediHelp Central Medical Center</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
