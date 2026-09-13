<?php
$page_title = "Patient Profile - MidiHelp";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Enforce login guard
require_login();

$user = get_logged_user();
$user_id = $user['id'];

$errors = [];
$success_msg = '';

// Fetch current user details from database
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    header("Location: logout.php");
    exit();
}

$user_data = $res->fetch_assoc();
$stmt->close();

// Process Profile Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = clean_input($_POST['full_name'], $conn);
    $phone = clean_input($_POST['phone'], $conn);
    $gender = clean_input($_POST['gender'], $conn);
    $date_of_birth = clean_input($_POST['date_of_birth'], $conn);
    $address = clean_input($_POST['address'], $conn);

    if (empty($full_name)) {
        $errors[] = "Full name cannot be empty.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number cannot be empty.";
    }

    if (empty($errors)) {
        $u_stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, gender = ?, date_of_birth = ?, address = ? WHERE id = ?");
        $u_stmt->bind_param("sssssi", $full_name, $phone, $gender, $date_of_birth, $address, $user_id);

        if ($u_stmt->execute()) {
            $_SESSION['user_name'] = $full_name;
            $success_msg = "Profile updated successfully!";
            
            // Refresh user data
            $user_data['full_name'] = $full_name;
            $user_data['phone'] = $phone;
            $user_data['gender'] = $gender;
            $user_data['date_of_birth'] = $date_of_birth;
            $user_data['address'] = $address;
        } else {
            $errors[] = "Failed to update profile. Error: " . $conn->error;
        }
        $u_stmt->close();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 20px; padding-bottom: 60px;">
    <div style="max-width: 650px; margin: 0 auto;">
        <!-- PAGE HEADER -->
        <div style="margin-bottom: 28px;">
            <h1 style="font-size: 2rem; font-weight: 800; color: var(--dark-text); margin-bottom: 4px;">My Profile Settings</h1>
            <p style="color: var(--muted-text); font-size: 0.95rem;">Manage your personal patient details and contact information.</p>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <span>✓ <?php echo htmlspecialchars($success_msg); ?></span>
                <button class="alert-close">✕</button>
            </div>
        <?php endif; ?>

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

        <!-- PROFILE FORM CARD -->
        <div style="background: var(--card-bg); padding: 36px; border-radius: var(--radius-lg); box-shadow: var(--shadow-md); border: 1px solid var(--border-color);">
            <form action="profile.php" method="POST">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="email" class="form-label">Email Address (Registered Account)</label>
                    <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled style="background-color: #E2E8F0; opacity: 0.8; font-weight: 600;">
                    <small style="color: var(--muted-text); font-size: 0.75rem; margin-top: 4px;">Email address cannot be modified.</small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($user_data['full_name']); ?>">
                </div>

                <div class="form-row" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="Male" <?php echo ($user_data['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo ($user_data['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo ($user_data['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control" value="<?php echo htmlspecialchars($user_data['date_of_birth'] ?? ''); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Save Profile Changes</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
