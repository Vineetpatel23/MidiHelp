<?php
// ========================================================
// MidiHelp - Database Configuration File
// Centralized MySQLi connection for the entire application
// ========================================================

// Turn off default automatic exception throwing so we can catch and format errors cleanly
mysqli_report(MYSQLI_REPORT_OFF);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Set your MySQL root password here if required (e.g. 'root' or 'admin')
define('DB_NAME', 'doctor_appointment_system');

// Attempt to connect to MySQL database
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// If connection failed with an empty password, try the local default password.
if ($conn->connect_error && DB_PASS === '') {
    $fallback_passwords = ['root'];
    foreach ($fallback_passwords as $pass) {
        $test_conn = @new mysqli(DB_HOST, DB_USER, $pass, DB_NAME);
        if (!$test_conn->connect_error) {
            $conn = $test_conn;
            break;
        }
    }
}

// Check if connection failed
if ($conn->connect_error) {
    die("
    <div style='font-family: Arial, sans-serif; padding: 30px; background: #FEF2F2; color: #991B1B; border: 1px solid #FCA5A5; border-radius: 12px; max-width: 650px; margin: 50px auto; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);'>
        <h2 style='margin-top: 0; color: #991B1B;'>⚠️ Database Connection Failed!</h2>
        <p><strong>Error Message:</strong> <code>" . htmlspecialchars($conn->connect_error) . "</code></p>
        <hr style='border: none; border-top: 1px solid #FCA5A5; margin: 20px 0;'>
        <h4 style='margin-bottom: 8px;'>How to fix this error:</h4>
        <ol style='line-height: 1.8; margin-left: 20px;'>
            <li><strong>If your MySQL root user has a password:</strong><br>Open <code>config/database.php</code> and set <code>define('DB_PASS', 'your_mysql_password');</code></li>
            <li><strong>If database <code>doctor_appointment_system</code> is missing:</strong><br>Import the SQL file into MySQL by running:<br><code style='background: #FEE2E2; padding: 4px 8px; border-radius: 4px;'>mysql -u root -p < database/doctor_appointment.sql</code></li>
        </ol>
    </div>
    ");
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");

// Helper function to sanitize user inputs globally
if (!function_exists('clean_input')) {
    function clean_input($data, $conn = null) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        if ($conn && $conn instanceof mysqli) {
            $data = $conn->real_escape_string($data);
        }
        return $data;
    }
}
?>
