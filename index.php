<?php
$page_title = "Home - Book Your Doctor Appointment Easily";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Fetch featured doctors (featuring Dr. Heer Patel & top specialists, limit 6)
$featured_doctors = [];
$query = "SELECT * FROM doctors WHERE status = 'Available' ORDER BY (name LIKE '%Heer Patel%') DESC, id ASC LIMIT 6";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featured_doctors[] = $row;
    }
}
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-text-content">
                <div class="hero-tag">
                    <span class="pulse-dot"></span> Healthcare Made Simple
                </div>
                <h1 class="hero-title">Book Your <span>Doctor Appointment</span> Easily</h1>
                <p class="hero-subtitle">
                    Find trusted doctors, choose your preferred date & time slot, and manage your family appointments from one simple, secure platform.
                </p>
                <div class="hero-buttons">
                    <a href="book-appointment.php" class="btn btn-primary btn-lg">Book Appointment Now</a>
                    <a href="doctors.php" class="btn btn-outline btn-lg">Find a Doctor</a>
                </div>
            </div>

            <div class="hero-image-wrapper">
                <!-- FLOATING BADGE -->
                <div class="floating-badge-1">
                    <span class="pulse-dot"></span>
                    <span>50+ Doctors Online</span>
                </div>

                <div class="hero-card-visual">
                    <div class="visual-badge">
                        <div class="badge-icon">🩺</div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.1rem; font-weight: 800;">Verified Medical Specialists</h4>
                            <p style="margin: 2px 0 0 0; font-size: 0.85rem; color: var(--muted-text);">Instant Online Booking & Zero Queue</p>
                        </div>
                    </div>
                    <div style="background: #F0FDFA; padding: 20px; border-radius: 14px; border: 1px dashed var(--secondary);">
                        <p style="margin: 0; font-weight: 800; color: var(--primary); font-size: 0.98rem;">⚡ Emergency Assistance Available</p>
                        <p style="margin: 6px 0 0 0; font-size: 0.88rem; color: var(--muted-text);">Call our helpline at <strong>1800-MIDIHELP</strong> for immediate medical guidance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SEARCH SECTION -->
<div class="container">
    <section class="search-section">
        <div class="search-box">
            <form action="doctors.php" method="GET" class="search-form">
                <div class="form-group">
                    <label for="search" class="form-label">Search Doctor</label>
                    <input type="text" id="search" name="search" class="form-control" placeholder="Search doctor by name or keyword...">
                </div>

                <div class="form-group">
                    <label for="specialization" class="form-label">Specialization</label>
                    <select id="specialization" name="specialization" class="form-control">
                        <option value="">All Specializations</option>
                        <option value="General Physician">General Physician</option>
                        <option value="Cardiologist">Cardiologist</option>
                        <option value="Dermatologist">Dermatologist</option>
                        <option value="Dentist">Dentist</option>
                        <option value="Pediatrician">Pediatrician</option>
                        <option value="Orthopedic">Orthopedic</option>
                        <option value="Neurologist">Neurologist</option>
                        <option value="Gynecologist">Gynecologist</option>
                        <option value="Radiologist">Radiologist</option>
                    </select>
                </div>

                <div class="form-group" style="justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-block" style="height: 50px;">Search Doctors</button>
                </div>
            </form>
        </div>
    </section>
</div>

<!-- POPULAR SPECIALIZATIONS -->
<section class="container" style="margin-bottom: 70px;">
    <div class="section-header">
        <div class="section-subtitle">Department Categories</div>
        <h2 class="section-title">Popular Specializations</h2>
    </div>

    <div class="specialties-grid">
        <a href="doctors.php?specialization=Cardiologist" class="specialty-card">
            <div class="specialty-icon">🫀</div>
            <div class="specialty-name">Cardiology</div>
            <div class="specialty-count">Heart Care Specialist</div>
        </a>

        <a href="doctors.php?specialization=Dentist" class="specialty-card">
            <div class="specialty-icon">🦷</div>
            <div class="specialty-name">Dentistry</div>
            <div class="specialty-count">Dental Surgery & Teeth</div>
        </a>

        <a href="doctors.php?specialization=Neurologist" class="specialty-card">
            <div class="specialty-icon">🧠</div>
            <div class="specialty-name">Neurology</div>
            <div class="specialty-count">Brain & Nerve System</div>
        </a>

        <a href="doctors.php?specialization=Orthopedic" class="specialty-card">
            <div class="specialty-icon">🦴</div>
            <div class="specialty-name">Orthopedics</div>
            <div class="specialty-count">Bones & Joint Surgery</div>
        </a>

        <a href="doctors.php?specialization=Pediatrician" class="specialty-card">
            <div class="specialty-icon">👶</div>
            <div class="specialty-name">Pediatrics</div>
            <div class="specialty-count">Child & Infant Care</div>
        </a>

        <a href="doctors.php?specialization=General+Physician" class="specialty-card">
            <div class="specialty-icon">🩺</div>
            <div class="specialty-name">General Medicine</div>
            <div class="specialty-count">Primary Family Care</div>
        </a>

        <a href="doctors.php?specialization=Radiologist" class="specialty-card">
            <div class="specialty-icon">🩻</div>
            <div class="specialty-name">Radiology</div>
            <div class="specialty-count">MRI, CT Scan & X-Ray</div>
        </a>
    </div>
</section>

<!-- FEATURED DOCTORS -->
<section class="container" style="margin-bottom: 70px;">
    <div class="section-header">
        <div class="section-subtitle">Top Rated Doctors</div>
        <h2 class="section-title">Featured Medical Specialists</h2>
    </div>

    <div class="doctors-grid">
        <?php if (!empty($featured_doctors)): ?>
            <?php foreach ($featured_doctors as $doc): ?>
                <div class="doctor-card">
                    <div class="doctor-card-img-wrapper">
                        <div class="doctor-card-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800;">
                            <?php 
                            $initials = implode('', array_map(function($w) { return $w[0]; }, explode(' ', str_replace('Dr. ', '', $doc['name']))));
                            echo htmlspecialchars(substr($initials, 0, 2));
                            ?>
                        </div>
                        <span class="doctor-status-badge <?php echo ($doc['status'] == 'Available') ? 'status-available' : 'status-unavailable'; ?>">
                            <span class="pulse-dot"></span> <?php echo htmlspecialchars($doc['status']); ?>
                        </span>
                    </div>

                    <div class="doctor-card-body">
                        <div class="doctor-spec"><?php echo htmlspecialchars($doc['specialization']); ?></div>
                        <h3 class="doctor-name"><?php echo htmlspecialchars($doc['name']); ?></h3>
                        <div class="doctor-meta">
                            <span>🎓 <?php echo htmlspecialchars($doc['qualification']); ?></span>
                            <span>⏳ <?php echo htmlspecialchars($doc['experience']); ?> Experience</span>
                        </div>

                        <div class="doctor-fee">
                            <span style="font-size: 0.85rem; color: var(--muted-text); font-weight: 600;">Consultation Fee</span>
                            <span class="fee-amount">₹<?php echo number_format($doc['consultation_fee'], 2); ?></span>
                        </div>
                    </div>

                    <div class="doctor-card-footer">
                        <a href="doctor-details.php?id=<?php echo $doc['id']; ?>" class="btn btn-outline btn-sm">View Profile</a>
                        <a href="book-appointment.php?doctor_id=<?php echo $doc['id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted" style="grid-column: 1/-1; text-align: center;">No featured doctors available at the moment.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center;">
        <a href="doctors.php" class="btn btn-secondary btn-lg">Browse All Doctors</a>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="container" id="about">
    <div class="how-it-works">
        <div class="section-header" style="margin-bottom: 36px;">
            <div class="section-subtitle">Simple 3-Step Process</div>
            <h2 class="section-title">How MidiHelp Works</h2>
        </div>

        <div class="steps-grid">
            <div class="step-item">
                <div class="step-num">1</div>
                <h3 class="step-title">Find a Doctor</h3>
                <p class="step-desc">Search by doctor name or specialization and explore detailed qualifications & reviews.</p>
            </div>

            <div class="step-item">
                <div class="step-num">2</div>
                <h3 class="step-title">Choose Date & Time</h3>
                <p class="step-desc">Pick your preferred appointment date and choose an available live time slot.</p>
            </div>

            <div class="step-item">
                <div class="step-num">3</div>
                <h3 class="step-title">Confirm Appointment</h3>
                <p class="step-desc">Enter patient symptoms, confirm details, and receive instant booking confirmation.</p>
            </div>
        </div>
    </div>
</section>

<!-- STATISTICS BAR -->
<div class="container" id="contact">
    <div class="stats-bar">
        <div class="stats-grid">
            <div>
                <div class="stat-number">500+</div>
                <div class="stat-label">Registered Patients</div>
            </div>
            <div>
                <div class="stat-number">50+</div>
                <div class="stat-label">Expert Doctors</div>
            </div>
            <div>
                <div class="stat-number">1000+</div>
                <div class="stat-label">Successful Appointments</div>
            </div>
            <div>
                <div class="stat-number">100%</div>
                <div class="stat-label">Patient Satisfaction</div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
