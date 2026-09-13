<?php
$page_title = "Find Doctors - MediHelp";
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/header.php';

// Retrieve filter inputs
$search = isset($_GET['search']) ? clean_input($_GET['search'], $conn) : '';
$specialization = isset($_GET['specialization']) ? clean_input($_GET['specialization'], $conn) : '';
$status = isset($_GET['status']) ? clean_input($_GET['status'], $conn) : '';

// Build dynamic SQL query
$query = "SELECT * FROM doctors WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ? OR qualification LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

if (!empty($specialization)) {
    $query .= " AND specialization = ?";
    $params[] = $specialization;
    $types .= "s";
}

if (!empty($status)) {
    $query .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$query .= " ORDER BY name ASC";

$stmt = $conn->prepare($query);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}
$stmt->close();
?>

<div class="container" style="padding-top: 20px;">
    <!-- PAGE HEADER -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2.2rem; font-weight: 800; color: var(--dark-text); margin-bottom: 6px;">Find Medical Doctors</h1>
        <p style="color: var(--muted-text); font-size: 1rem;">Browse experienced healthcare specialists and book an appointment online.</p>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="search-box" style="margin-bottom: 40px;">
        <form action="doctors.php" method="GET" class="search-form">
            <div class="form-group">
                <label for="search" class="form-label">Doctor Name / Keyword</label>
                <input type="text" id="search" name="search" class="form-control" placeholder="Search doctor by name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="form-group">
                <label for="specialization" class="form-label">Specialization</label>
                <select id="specialization" name="specialization" class="form-control">
                    <option value="">All Specializations</option>
                    <?php
                    $specs = ["General Physician", "Cardiologist", "Dermatologist", "Dentist", "Pediatrician", "Orthopedic", "Neurologist", "Gynecologist", "Radiologist"];
                    foreach ($specs as $spec) {
                        $selected = ($specialization === $spec) ? 'selected' : '';
                        echo '<option value="' . $spec . '" ' . $selected . '>' . $spec . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Availability</label>
                <select id="status" name="status" class="form-control">
                    <option value="">All Availability</option>
                    <option value="Available" <?php echo ($status === 'Available') ? 'selected' : ''; ?>>Available</option>
                    <option value="Unavailable" <?php echo ($status === 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>

            <div class="form-group" style="grid-column: 1/-1; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">Apply Filters</button>
                <a href="doctors.php" class="btn btn-outline" style="padding: 10px 20px;">Reset</a>
            </div>
        </form>
    </div>

    <!-- DOCTORS GRID -->
    <div class="doctors-grid">
        <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doc): ?>
                <div class="doctor-card">
                    <div class="doctor-card-img-wrapper">
                        <div class="doctor-card-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 800;">
                            <?php 
                            $initials = implode('', array_map(function($w) { return $w[0]; }, explode(' ', str_replace('Dr. ', '', $doc['name']))));
                            echo htmlspecialchars(substr($initials, 0, 2));
                            ?>
                        </div>
                        <span class="doctor-status-badge <?php echo ($doc['status'] == 'Available') ? 'status-available' : 'status-unavailable'; ?>">
                            <?php echo htmlspecialchars($doc['status']); ?>
                        </span>
                    </div>

                    <div class="doctor-card-body">
                        <div class="doctor-spec"><?php echo htmlspecialchars($doc['specialization']); ?></div>
                        <h3 class="doctor-name"><?php echo htmlspecialchars($doc['name']); ?></h3>
                        <div class="doctor-meta">
                            <span>🎓 <?php echo htmlspecialchars($doc['qualification']); ?></span>
                            <span>⏳ <?php echo htmlspecialchars($doc['experience']); ?> Experience</span>
                            <span>🗓️ <?php echo htmlspecialchars($doc['available_days']); ?></span>
                        </div>

                        <div class="doctor-fee">
                            <span style="font-size: 0.85rem; color: var(--muted-text); font-weight: 500;">Consultation Fee</span>
                            <span class="fee-amount">₹<?php echo number_format($doc['consultation_fee'], 2); ?></span>
                        </div>
                    </div>

                    <div class="doctor-card-footer">
                        <a href="doctor-details.php?id=<?php echo $doc['id']; ?>" class="btn btn-outline btn-sm">View Profile</a>
                        <?php if ($doc['status'] === 'Available'): ?>
                            <a href="book-appointment.php?doctor_id=<?php echo $doc['id']; ?>" class="btn btn-primary btn-sm">Book Now</a>
                        <?php else: ?>
                            <button class="btn btn-outline btn-sm" disabled style="opacity: 0.5;">Unavailable</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; background: var(--card-bg); padding: 40px; text-align: center; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                <h3 style="color: var(--dark-text); margin-bottom: 8px;">No Doctors Found</h3>
                <p style="color: var(--muted-text); margin-bottom: 20px;">No doctors matched your filter criteria. Try adjusting your search query.</p>
                <a href="doctors.php" class="btn btn-primary">View All Doctors</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
