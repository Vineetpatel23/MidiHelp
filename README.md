# MidiHelp - Doctor Appointment System

A complete, modern, student-friendly **Doctor Appointment System** web application developed for academic project demonstration and practical viva defense.

---

## 1. Project Overview

**MidiHelp** is a web-based doctor appointment booking platform designed for patients to search verified medical doctors, select consultation dates, pick real-time available time slots, and confirm appointments online. The system features a full-featured **Patient Portal** and **Admin Dashboard** with real-time slot checking, duplicate booking prevention, complete doctor CRUD management, and interactive patient dashboard.

---

## 2. Key Features

### 👤 Patient Features
- **Home Landing Page**: Hero banner, search bar, specialty category cards, featured doctors, statistics, and 3-step process.
- **Doctor Search & Filtering**: Instant filter doctors by doctor name, specialization, and availability status.
- **Detailed Doctor Profiles**: View qualifications, years of experience, consultation fee, working days, hours, and direct booking CTA.
- **Dynamic Time Slot Selection**: Pick an appointment date (past dates restricted) and dynamically view/select available 30-minute time slots.
- **Duplicate Slot Prevention**: Automatically checks MySQL database to prevent two patients from booking the exact same doctor, date, and time slot.
- **Patient Authentication**: Secure user registration and login powered by PHP `password_hash()` and `password_verify()`.
- **Patient Dashboard**: View appointment history, status badges (`Pending`, `Confirmed`, `Completed`, `Cancelled`), and printable appointment receipts.
- **Profile Management**: Update patient personal information and contact details.

### 🛡️ Admin Features
- **Secure Admin Authentication**: Protected login (`admin` / `admin123`) using session guards.
- **System Dashboard**: Live analytic metrics showing Total Doctors, Total Patients, Total Appointments, and status breakdown.
- **Doctor Management (CRUD)**: Add new doctors, edit doctor information/schedules, delete doctors, or toggle availability status.
- **Patient Directory**: View list of registered patients, contact numbers, and account registration dates.
- **Appointment Status Management**: Change appointment status instantly between `Pending`, `Confirmed`, `Completed`, and `Cancelled`.

---

## 3. Technology Stack

- **Frontend**: HTML5, Vanilla CSS3 (Custom design system, Teal `#0F766E` palette, Flexbox, Grid, CSS Media Queries), Vanilla JavaScript (No frameworks or libraries used).
- **Backend**: PHP (Procedural/Object-Oriented with MySQLi extension).
- **Database**: MySQL (`doctor_appointment_system`).
- **Server Support**: XAMPP, MAMP, WAMP, or PHP Built-in Development Server.

---

## 4. Folder Structure

```
doctor-appointment-system/
├── index.php                 # Home page
├── doctors.php               # Doctor listing & filtering
├── doctor-details.php        # Doctor detailed profile
├── book-appointment.php      # Appointment booking form
├── get-booked-slots.php      # AJAX endpoint for booked time slots
├── appointment-success.php   # Booking confirmation view
├── login.php                 # Patient login page
├── register.php              # Patient registration page
├── logout.php                # Patient logout handler
├── profile.php               # Patient profile page
├── appointments.php          # Patient dashboard & appointment history
├── admin/
│   ├── login.php             # Admin login
│   ├── dashboard.php         # Admin metrics dashboard
│   ├── doctors.php           # Manage doctors list
│   ├── add-doctor.php        # Add doctor form
│   ├── edit-doctor.php       # Edit doctor form
│   ├── delete-doctor.php     # Delete doctor handler
│   ├── patients.php          # Patient directory
│   ├── appointments.php      # Appointment management
│   ├── update-appointment.php# Status updater script
│   └── logout.php            # Admin logout
├── config/
│   └── database.php          # Central MySQLi database connection
├── includes/
│   ├── header.php            # HTML header & flash alerts
│   ├── footer.php            # HTML footer & script inclusions
│   ├── navbar.php            # Navigation bar
│   ├── auth.php              # Patient session guard
│   └── admin-auth.php        # Admin session guard
├── css/
│   ├── style.css             # Core design system & variables
│   ├── responsive.css        # Responsive media queries
│   └── admin.css             # Admin dashboard styling
├── js/
│   ├── main.js               # Menu toggle, password toggle, alerts
│   ├── validation.js         # Real-time form validation
│   └── appointment.js        # Date restrictions & dynamic slot picker
├── database/
│   └── doctor_appointment.sql# Database SQL import script
└── README.md                 # Complete documentation
```

---

## 5. Database Setup & Installation

### Option A: Using XAMPP / WAMP / MAMP (phpMyAdmin)
1. Start **Apache** and **MySQL** services in your control panel.
2. Open your web browser and navigate to `http://localhost/phpmyadmin`.
3. Click **Import** in phpMyAdmin.
4. Choose the SQL file located at `database/doctor_appointment.sql` and click **Go**.
5. Copy the project folder `doctor-appointment-system` into your server's root directory:
   - XAMPP: `htdocs/doctor-appointment-system`
   - WAMP: `www/doctor-appointment-system`
   - MAMP: `htdocs/doctor-appointment-system`
6. Open browser and go to `http://localhost/doctor-appointment-system/`.

### Option B: Using PHP Built-in Web Server
1. Create the MySQL database using MySQL CLI:
   ```bash
   mysql -u root -p < database/doctor_appointment.sql
   ```
2. Navigate into the project folder in terminal:
   ```bash
   cd doctor-appointment-system
   ```
3. Start PHP's built-in web server:
   ```bash
   php -S localhost:8000
   ```
4. Open `http://localhost:8000/` in your web browser.

---

## 6. Default Login Credentials

### Admin Login
- **URL**: `http://localhost:8000/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

### Sample Patient Login
- **URL**: `http://localhost:8000/login.php`
- **Email**: `john.doe@example.com`
- **Password**: `patient123`

---

## 7. Key PHP & MySQL Concepts Demonstrated (Viva Tips)

1. **Database Abstraction**: Centralized connection using `MySQLi` in `config/database.php` included via `require_once`.
2. **Prepared Statements (`bind_param`)**: Prevents SQL injection across all dynamic SQL queries.
3. **Password Security**: Uses PHP's native `password_hash($password, PASSWORD_DEFAULT)` and `password_verify()`.
4. **Session Management**: Session guards (`auth.php` and `admin-auth.php`) protect restricted pages from unauthenticated access.
5. **Relational Database Design**: Foreign key relationships (`users` -> `appointments` and `doctors` -> `appointments`).
6. **AJAX Asynchronous Requests**: `fetch()` API calls to `get-booked-slots.php` for live checking without full page reloads.
