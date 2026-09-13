<?php
$page_title = "Admin Login - MidiHelp";
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-auth.php';

// Redirect if admin already logged in
if (is_admin_logged_in()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'], $conn);
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both admin username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $admin = $res->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['flash_success'] = "Welcome to Admin Dashboard, " . htmlspecialchars($admin['username']) . "!";
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid admin password.";
            }
        } else {
            $error = "Admin account not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MidiHelp</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background-color: #0F172A; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">

    <div style="background: white; width: 100%; max-width: 440px; padding: 40px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);">
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 50px; height: 50px; background: #0F766E; color: white; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800; margin-bottom: 12px;">+</div>
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #0F172A; margin: 0;">Admin Portal</h2>
            <p style="color: #64748B; font-size: 0.9rem; margin-top: 4px;">Sign in to manage MidiHelp platform</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="username" class="form-label">Admin Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="admin" required value="admin">
            </div>

            <div class="form-group password-group" style="margin-bottom: 24px;">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required value="admin123">
                <button type="button" class="password-toggle" data-target="password">Show</button>
            </div>

            <div style="background: #F1F5F9; padding: 12px; border-radius: 8px; font-size: 0.8rem; color: #475569; margin-bottom: 24px; border: 1px solid #CBD5E1;">
                💡 <strong>Demo Credentials:</strong><br>
                Username: <code>admin</code> | Password: <code>admin123</code>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block">Log In to Admin Panel</button>
        </form>

        <div style="text-align: center; margin-top: 24px;">
            <a href="../index.php" style="font-size: 0.85rem; color: #64748B; text-decoration: none;">← Back to Main Website</a>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
