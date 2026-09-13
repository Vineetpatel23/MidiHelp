<?php
$page_title = "Book Doctor Appointment - MidiHelp";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Fetch all available doctors for the selection dropdown
$doctors_list = [];
$doc_res = $conn->query("SELECT id, name, specialization, consultation_fee FROM doctors WHERE status = 'Available' ORDER BY name ASC");
if ($doc_res) {
    while ($row = $doc_res->fetch_assoc()) {
        $doctors_list[] = $row;
    }
}

// Pre-selected doctor ID
$selected_doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;
if ($selected_doctor_id == 0 && !empty($doctors_list)) {
    $selected_doctor_id = $doctors_list[0]['id'];
}

// If patient user is logged in, pre-fill user info
$logged_user = get_logged_user();
$user_id = $logged_user ? $logged_user['id'] : null;

$patient_name = '';
$patient_email = '';
$patient_phone = '';

if ($user_id) {
    $u_stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
    $u_stmt->bind_param("i", $user_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    if ($u_row = $u_res->fetch_assoc()) {
        $patient_name = $u_row['full_name'];
        $patient_email = $u_row['email'];
        $patient_phone = $u_row['phone'];
    }
    $u_stmt->close();
}

$errors = [];

// Form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = isset($_POST['doctor_id']) ? intval($_POST['doctor_id']) : 0;
    $appointment_date = isset($_POST['appointment_date']) ? clean_input($_POST['appointment_date'], $conn) : '';
    $appointment_time = isset($_POST['appointment_time']) ? clean_input($_POST['appointment_time'], $conn) : '';
    $patient_name = isset($_POST['patient_name']) ? clean_input($_POST['patient_name'], $conn) : '';
    $patient_email = isset($_POST['patient_email']) ? clean_input($_POST['patient_email'], $conn) : '';
    $patient_phone = isset($_POST['patient_phone']) ? clean_input($_POST['patient_phone'], $conn) : '';
    $symptoms = isset($_POST['symptoms']) ? clean_input($_POST['symptoms'], $conn) : '';

    // SERVER-SIDE VALIDATION
    if ($doctor_id <= 0) {
        $errors[] = "Please select a valid doctor.";
    }
    if (empty($appointment_date)) {
        $errors[] = "Please select an appointment date.";
    } elseif ($appointment_date < date('Y-m-d')) {
        $errors[] = "Appointment date cannot be in the past.";
    }
    if (empty($appointment_time)) {
        $errors[] = "Please select an available time slot.";
    }
    if (empty($patient_name)) {
        $errors[] = "Please enter the patient's full name.";
    }
    if (empty($patient_email) || !filter_var($patient_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid patient email address.";
    }
    if (empty($patient_phone)) {
        $errors[] = "Please enter a valid phone number.";
    }

    // DUPLICATE SLOT CHECK (Doctor + Date + Time)
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'Cancelled'");
        $check_stmt->bind_param("iss", $doctor_id, $appointment_date, $appointment_time);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();

        if ($check_res->num_rows > 0) {
            $errors[] = "This appointment time slot is already booked for Dr. Please choose another time slot or date.";
        }
        $check_stmt->close();
    }

    // INSERT APPOINTMENT IF ALL VALID
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, patient_name, patient_email, patient_phone, symptoms, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("iissssss", $user_id, $doctor_id, $appointment_date, $appointment_time, $patient_name, $patient_email, $patient_phone, $symptoms);

        if ($stmt->execute()) {
            $booking_id = $stmt->insert_id;
            $stmt->close();
            header("Location: appointment-success.php?id=" . $booking_id);
            exit();
        } else {
            $errors[] = "Failed to process appointment booking. Database error: " . $conn->error;
        }
    }

    $selected_doctor_id = $doctor_id;
}

$extra_js = "js/appointment.js";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 20px;">
    <div style="max-width: 840px; margin: 0 auto;">
        <!-- HEADER -->
        <div style="margin-bottom: 28px; text-align: center;">
            <h1 style="font-size: 2.4rem; font-weight: 800; color: var(--dark-text); margin-bottom: 8px;">Book Doctor Appointment</h1>
            <p style="color: var(--muted-text); font-size: 1rem;">Complete the steps below to reserve your live consultation slot.</p>
        </div>

        <!-- 3-STEP PROGRESS VISUAL BAR -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 32px;">
            <div style="background: var(--card-bg); padding: 14px 16px; border-radius: var(--radius-md); border-left: 4px solid var(--primary); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 10px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">1</div>
                <div>
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--primary); text-transform: uppercase;">Step 1</div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: var(--dark-text);">Doctor & Date</div>
                </div>
            </div>

            <div style="background: var(--card-bg); padding: 14px 16px; border-radius: var(--radius-md); border-left: 4px solid var(--secondary); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 10px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--secondary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">2</div>
                <div>
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase;">Step 2</div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: var(--dark-text);">Time Slot</div>
                </div>
            </div>

            <div style="background: var(--card-bg); padding: 14px 16px; border-radius: var(--radius-md); border-left: 4px solid var(--dark-text); box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 10px;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--dark-text); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">3</div>
                <div>
                    <div style="font-size: 0.8rem; font-weight: 800; color: var(--dark-text); text-transform: uppercase;">Step 3</div>
                    <div style="font-size: 0.88rem; font-weight: 700; color: var(--dark-text);">Patient Info</div>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 6px 0 0 18px; list-style-type: disc;">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button class="alert-close">✕</button>
            </div>
        <?php endif; ?>

        <!-- BOOKING CARD FORM -->
        <div style="background: var(--glass-bg); backdrop-filter: blur(16px); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 1px solid rgba(255, 255, 255, 0.9); margin-bottom: 60px;">
            <form action="book-appointment.php" method="POST" id="bookingForm" onsubmit="return validateRegistrationForm(this)">
                
                <!-- 1. DOCTOR SELECTION -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="doctor_id" class="form-label">Select Medical Doctor *</label>
                    <select name="doctor_id" id="doctor_id" class="form-control" required style="font-weight: 700; padding: 14px;">
                        <option value="">-- Select Doctor --</option>
                        <?php foreach ($doctors_list as $doc): ?>
                            <option value="<?php echo $doc['id']; ?>" <?php echo ($selected_doctor_id == $doc['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doc['name']) . " (" . htmlspecialchars($doc['specialization']) . ") - ₹" . number_format($doc['consultation_fee'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. DATE & TIME SLOTS -->
                <div class="form-row" style="margin-bottom: 24px;">
                    <div class="form-group">
                        <label for="appointment_date" class="form-label">Appointment Date *</label>
                        <input type="date" id="appointment_date" name="appointment_date" class="form-control" required value="<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Selected Time Slot *</label>
                        <input type="text" id="appointment_time" name="appointment_time" class="form-control" readonly placeholder="Click a slot below" required value="<?php echo isset($_POST['appointment_time']) ? htmlspecialchars($_POST['appointment_time']) : ''; ?>" style="font-weight: 800; color: var(--primary); background-color: var(--card-bg);">
                    </div>
                </div>

                <!-- DYNAMIC TIME SLOTS CONTAINER -->
                <div style="margin-bottom: 32px;">
                    <label class="form-label" style="margin-bottom: 12px; display: block;">Available Time Slots (Click to Select)</label>
                    <div id="timeSlotsContainer" class="time-slots-container">
                        <!-- Populated dynamically via js/appointment.js -->
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color); margin: 36px 0;">

                <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--dark-text); margin-bottom: 24px;">Patient Contact Information</h3>

                <!-- 3. PATIENT INFO -->
                <div class="form-row" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="patient_name" class="form-label">Patient Full Name *</label>
                        <input type="text" id="patient_name" name="patient_name" class="form-control" placeholder="John Doe" required value="<?php echo htmlspecialchars($patient_name); ?>">
                    </div>

                    <div class="form-group">
                        <label for="patient_email" class="form-label">Email Address *</label>
                        <input type="email" id="patient_email" name="patient_email" class="form-control" placeholder="john@example.com" required value="<?php echo htmlspecialchars($patient_email); ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="patient_phone" class="form-label">Phone Number *</label>
                    <input type="tel" id="patient_phone" name="patient_phone" class="form-control" placeholder="+1 (555) 000-0000" required value="<?php echo htmlspecialchars($patient_phone); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 32px;">
                    <label for="symptoms" class="form-label">Symptoms / Reason for Visit (Optional)</label>
                    <textarea id="symptoms" name="symptoms" class="form-control" rows="4" placeholder="Briefly describe your symptoms or medical concern..."><?php echo isset($_POST['symptoms']) ? htmlspecialchars($_POST['symptoms']) : ''; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Confirm Appointment Booking</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
