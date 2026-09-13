/**
 * MidiHelp - Main Vanilla JavaScript
 * Handles navigation, dismissible alerts, password toggles, and UI triggers
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Menu Hamburger Toggle
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            const isOpen = navMenu.classList.contains('active');
            navToggle.setAttribute('aria-expanded', isOpen);
            navToggle.innerHTML = isOpen ? '✕' : '☰';
        });
    }

    // 2. Dismissible Flash Alerts
    const alertCloses = document.querySelectorAll('.alert-close');
    alertCloses.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alertBox = btn.closest('.alert');
            if (alertBox) {
                alertBox.style.opacity = '0';
                setTimeout(function () {
                    alertBox.remove();
                }, 200);
            }
        });
    });

    // 3. Password Show / Hide Toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    btn.textContent = 'Show';
                }
            }
        });
    });
});

/**
 * Global Delete Confirmation helper
 */
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item? This action cannot be undone.');
}
