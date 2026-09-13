<?php
require_once __DIR__ . '/auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="navbar">
    <div class="container">
        <a href="index.php" class="brand-logo">
            <div class="logo-icon">+</div>
            <span>MidiHelp</span>
        </a>

        <nav class="nav-menu" id="navMenu">
            <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
            <a href="doctors.php" class="nav-link <?php echo ($current_page == 'doctors.php') ? 'active' : ''; ?>">Doctors</a>
            <a href="book-appointment.php" class="nav-link <?php echo ($current_page == 'book-appointment.php') ? 'active' : ''; ?>">Book Appointment</a>
            <?php if (is_logged_in()): ?>
                <a href="appointments.php" class="nav-link <?php echo ($current_page == 'appointments.php') ? 'active' : ''; ?>">My Appointments</a>
            <?php endif; ?>
            <a href="index.php#about" class="nav-link">About</a>
            <a href="index.php#contact" class="nav-link">Contact</a>

            <div class="nav-auth-buttons">
                <?php if (is_logged_in()): ?>
                    <a href="profile.php" class="btn btn-outline btn-sm">Profile</a>
                    <a href="logout.php" class="btn btn-primary btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </nav>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu">☰</button>
    </div>
</header>
