<?php
$page_title = "Patient Login - MidiHelp";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: appointments.php");
    exit();
}

$error = '';
$email = '';
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'], $conn);
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set Session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];

                $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['full_name']) . "!";
                
                if (!empty($redirect)) {
                    header("Location: " . urldecode($redirect));
                } else {
                    header("Location: appointments.php");
                }
                exit();
            } else {
                $error = "Invalid password. Please check your credentials.";
            }
        } else {
            $error = "No patient account found with that email address.";
        }
        $stmt->close();
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Patient Login</h1>
            <p class="auth-subtitle">Sign in to access your appointments and medical profile</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <span><?php echo htmlspecialchars($error); ?></span>
                <button class="alert-close">✕</button>
            </div>
        <?php endif; ?>

        <form action="login.php<?php echo !empty($redirect) ? '?redirect=' . urlencode($redirect) : ''; ?>" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required value="<?php echo htmlspecialchars($email); ?>">
            </div>

            <div class="form-group password-group" style="margin-bottom: 24px;">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                <button type="button" class="password-toggle" data-target="password">Show</button>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Sign In</button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--muted-text);">
            Don't have an account? <a href="register.php" style="font-weight: 700;">Register Here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
