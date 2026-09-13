<?php
$page_title = "Patient Registration - MidiHelp";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: appointments.php");
    exit();
}

$errors = [];
$full_name = '';
$email = '';
$phone = '';
$gender = 'Male';
$date_of_birth = '';
$address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean_input($_POST['full_name'], $conn);
    $email = clean_input($_POST['email'], $conn);
    $phone = clean_input($_POST['phone'], $conn);
    $gender = clean_input($_POST['gender'], $conn);
    $date_of_birth = clean_input($_POST['date_of_birth'], $conn);
    $address = clean_input($_POST['address'], $conn);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // SERVER-SIDE VALIDATION
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // CHECK IF EMAIL ALREADY REGISTERED
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        if ($check_res->num_rows > 0) {
            $errors[] = "This email address is already registered. Please login instead.";
        }
        $check_stmt->close();
    }

    // INSERT NEW USER
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, gender, date_of_birth, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $full_name, $email, $phone, $hashed_password, $gender, $date_of_birth, $address);

        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['flash_success'] = "Registration successful! You can now log in to your account.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed. Database error: " . $conn->error;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 20px; padding-bottom: 60px;">
    <div class="auth-card" style="max-width: 600px;">
        <div class="auth-header">
            <h1 class="auth-title">Create Patient Account</h1>
            <p class="auth-subtitle">Register to book doctors and manage your medical appointments</p>
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

        <form action="register.php" method="POST" onsubmit="return validateRegistrationForm(this)">
            <div class="form-group" style="margin-bottom: 18px;">
                <label for="full_name" class="form-label">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required value="<?php echo htmlspecialchars($full_name); ?>">
            </div>

            <div class="form-row" style="margin-bottom: 18px;">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="+19876543210" required value="<?php echo htmlspecialchars($phone); ?>">
                </div>
            </div>

            <div class="form-row" style="margin-bottom: 18px;">
                <div class="form-group">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-control">
                        <option value="Male" <?php echo ($gender === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($gender === 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($gender === 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($date_of_birth); ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 18px;">
                <label for="address" class="form-label">Residential Address</label>
                <textarea id="address" name="address" class="form-control" rows="2" placeholder="Street address, city, state..."><?php echo htmlspecialchars($address); ?></textarea>
            </div>

            <div class="form-row" style="margin-bottom: 24px;">
                <div class="form-group password-group">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="At least 6 characters" required>
                    <button type="button" class="password-toggle" data-target="password">Show</button>
                </div>

                <div class="form-group password-group">
                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    <button type="button" class="password-toggle" data-target="confirm_password">Show</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Register Account</button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--muted-text);">
            Already have an account? <a href="login.php" style="font-weight: 700;">Login Here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
