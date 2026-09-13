</main>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <div class="logo-icon">+</div>
                    <span>MidiHelp</span>
                </div>
                <p class="footer-desc">
                    MidiHelp makes healthcare booking effortless. Find top specialists, choose your convenient date & time slot, and manage patient appointments seamlessly.
                </p>
            </div>

            <div>
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="doctors.php">Find Doctors</a></li>
                    <li><a href="book-appointment.php">Book Appointment</a></li>
                    <li><a href="login.php">Patient Portal</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-heading">Specialties</h4>
                <ul class="footer-links">
                    <li><a href="doctors.php?specialization=Cardiologist">Cardiology</a></li>
                    <li><a href="doctors.php?specialization=Dermatologist">Dermatology</a></li>
                    <li><a href="doctors.php?specialization=General+Physician">General Medicine</a></li>
                    <li><a href="doctors.php?specialization=Pediatrician">Pediatrics</a></li>
                    <li><a href="doctors.php?specialization=Dentist">Dentistry</a></li>
                </ul>
            </div>

            <div>
                <h4 class="footer-heading">Opening Hours</h4>
                <p style="font-size: 0.9rem; margin-bottom: 8px;">Monday - Friday: 08:00 AM - 08:00 PM</p>
                <p style="font-size: 0.9rem; margin-bottom: 8px;">Saturday: 09:00 AM - 05:00 PM</p>
                <p style="font-size: 0.9rem; color: #F59E0B;">Emergency Care: 24/7 Available</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MidiHelp Doctor Appointment System. College Project Demonstration.</p>
        </div>
    </div>
</footer>

<!-- Core JavaScript Files -->
<script src="js/main.js"></script>
<script src="js/validation.js"></script>
<?php if (isset($extra_js)): ?>
    <script src="<?php echo htmlspecialchars($extra_js); ?>"></script>
<?php endif; ?>
</body>
</html>
