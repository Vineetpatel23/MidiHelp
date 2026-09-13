-- ========================================================
-- MidiHelp - Doctor Appointment System Database Script
-- Database Name: doctor_appointment_system
-- ========================================================

CREATE DATABASE IF NOT EXISTS `doctor_appointment_system` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `doctor_appointment_system`;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
  `date_of_birth` DATE DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `doctors`
-- --------------------------------------------------------
CREATE TABLE `doctors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `specialization` VARCHAR(100) NOT NULL,
  `qualification` VARCHAR(100) NOT NULL,
  `experience` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `consultation_fee` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `available_days` VARCHAR(100) NOT NULL DEFAULT 'Monday - Friday',
  `available_time` VARCHAR(100) NOT NULL DEFAULT '09:00 AM - 05:00 PM',
  `image` VARCHAR(255) DEFAULT 'default-doctor.png',
  `description` TEXT DEFAULT NULL,
  `status` ENUM('Available', 'Unavailable') NOT NULL DEFAULT 'Available',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `appointments`
-- --------------------------------------------------------
CREATE TABLE `appointments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `doctor_id` INT(11) NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` VARCHAR(20) NOT NULL,
  `patient_name` VARCHAR(100) NOT NULL,
  `patient_email` VARCHAR(100) NOT NULL,
  `patient_phone` VARCHAR(20) NOT NULL,
  `symptoms` TEXT DEFAULT NULL,
  `status` ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_appointments_users` (`user_id`),
  KEY `fk_appointments_doctors` (`doctor_id`),
  CONSTRAINT `fk_appointments_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_doctors` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------
CREATE TABLE `admins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- INSERT INITIAL SAMPLE DATA
-- ========================================================

-- Insert Admin Account (Username: admin, Password: admin123)
-- Password hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$12$uE1yV/DwRXT2815SKP/tPeed69hCR0mMKpdpMQ.Vm0Iy3YnRBaQlC');

-- Insert Sample Users (Password: patient123)
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `gender`, `date_of_birth`, `address`) VALUES
(1, 'John Doe', 'john.doe@example.com', '+19876543210', '$2y$10$eE0mE2wO5K5G7H5zXzX5e.U2/k5g2/k5g2/k5g2/k5g2/k5g2/k5g', 'Male', '1992-05-15', '123 Health Ave, Suite 4, New York'),
(2, 'Sarah Smith', 'sarah.smith@example.com', '+19876543211', '$2y$10$eE0mE2wO5K5G7H5zXzX5e.U2/k5g2/k5g2/k5g2/k5g2/k5g2/k5g', 'Female', '1995-08-22', '456 Medical Parkway, Boston');

-- Insert Sample Doctors
INSERT INTO `doctors` (`id`, `name`, `specialization`, `qualification`, `experience`, `phone`, `email`, `consultation_fee`, `available_days`, `available_time`, `image`, `description`, `status`) VALUES
(1, 'Dr. Alexander Wright', 'Cardiologist', 'MBBS, MD (Cardiology), FACC', '14 Years', '+1 555-0192', 'alexander.wright@midihelp.com', 120.00, 'Monday - Friday', '09:00 AM - 04:30 PM', 'doctor1.jpg', 'Senior Consultant Cardiologist specializing in preventive cardiology, echocardiography, hypertension management, and heart failure therapy.', 'Available'),

(2, 'Dr. Elena Rostova', 'Dermatologist', 'MBBS, MD (Dermatology)', '10 Years', '+1 555-0143', 'elena.rostova@midihelp.com', 95.00, 'Monday - Saturday', '10:00 AM - 05:00 PM', 'doctor2.jpg', 'Expert clinical dermatologist offering comprehensive treatment for acne, eczema, psoriasis, skin allergies, and advanced laser therapies.', 'Available'),

(3, 'Dr. Marcus Vance', 'General Physician', 'MBBS, MD (General Medicine)', '12 Years', '+1 555-0188', 'marcus.vance@midihelp.com', 75.00, 'Monday - Saturday', '08:30 AM - 04:00 PM', 'doctor3.jpg', 'Compassionate primary care specialist focused on annual health checkups, chronic disease management, diabetes treatment, and preventative health.', 'Available'),

(4, 'Dr. Priya Sharma', 'Pediatrician', 'MBBS, DCH, MD (Pediatrics)', '9 Years', '+1 555-0167', 'priya.sharma@midihelp.com', 85.00, 'Monday - Friday', '09:30 AM - 03:30 PM', 'doctor4.jpg', 'Dedicated pediatrician specializing in newborn care, child development assessment, childhood immunizations, and pediatric nutritional health.', 'Available'),

(5, 'Dr. David Chen', 'Dentist', 'BDS, MDS (Orthodontics)', '11 Years', '+1 555-0122', 'david.chen@midihelp.com', 90.00, 'Tuesday - Sunday', '10:00 AM - 06:00 PM', 'doctor5.jpg', 'Specialist dental surgeon providing root canal procedures, cosmetic dentistry, teeth whitening, aligners, and preventative oral care.', 'Available'),

(6, 'Dr. Sophia Martinez', 'Neurologist', 'MBBS, DM (Neurology)', '15 Years', '+1 555-0176', 'sophia.martinez@midihelp.com', 140.00, 'Monday - Thursday', '09:00 AM - 03:00 PM', 'doctor6.jpg', 'Consultant neurologist with extensive research background in migraine treatment, epilepsy management, stroke rehabilitation, and neuropathy.', 'Available'),

(7, 'Dr. Robert Taylor', 'Orthopedic', 'MBBS, MS (Orthopedics)', '13 Years', '+1 555-0155', 'robert.taylor@midihelp.com', 110.00, 'Monday - Friday', '09:00 AM - 05:00 PM', 'doctor7.jpg', 'Expert orthopedic surgeon specializing in joint replacement, sports injury treatment, fracture management, and arthroscopic procedures.', 'Available'),

(8, 'Dr. Amara Okafor', 'Gynecologist', 'MBBS, MS (Obstetrics & Gynecology)', '8 Years', '+1 555-0199', 'amara.okafor@midihelp.com', 100.00, 'Monday - Saturday', '10:00 AM - 04:30 PM', 'doctor8.jpg', 'Specialist obstetrician and gynecologist providing prenatal care, high-risk pregnancy management, hormonal balancing, and minimally invasive surgeries.', 'Available'),

(9, 'Dr. Heer Patel', 'Radiologist', 'MBBS, MD (Radiodiagnosis)', '4 Years', '+1 555-0182', 'heer.patel@midihelp.com', 95.00, 'Monday - Friday', '09:00 AM - 04:00 PM', 'doctor9.jpg', 'Specialist Radiologist expert in diagnostic imaging, MRI, CT scans, ultrasound interpretation, and interventional radiology procedures.', 'Available');

-- Insert Sample Appointments
INSERT INTO `appointments` (`id`, `user_id`, `doctor_id`, `appointment_date`, `appointment_time`, `patient_name`, `patient_email`, `patient_phone`, `symptoms`, `status`, `created_at`) VALUES
(1, 1, 1, CURRENT_DATE + INTERVAL 1 DAY, '10:00 AM', 'John Doe', 'john.doe@example.com', '+19876543210', 'Mild chest tightness during exercise and Routine cardiac checkup.', 'Confirmed', NOW()),
(2, 2, 2, CURRENT_DATE + INTERVAL 2 DAY, '02:00 PM', 'Sarah Smith', 'sarah.smith@example.com', '+19876543211', 'Persistent skin rash on arms and consultation for allergy test.', 'Pending', NOW()),
(3, 1, 3, CURRENT_DATE - INTERVAL 3 DAY, '09:30 AM', 'John Doe', 'john.doe@example.com', '+19876543210', 'Seasonal allergy symptoms and mild fever.', 'Completed', NOW());
