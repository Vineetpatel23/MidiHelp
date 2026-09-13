/**
 * MidiHelp - Form Validation JavaScript
 * Provides real-time client-side validation for registration, booking, and profile forms
 */

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function validatePhone(phone) {
    const re = /^[\+\d\s\(\)\-]{7,20}$/;
    return re.test(String(phone));
}

function validateRegistrationForm(formElement) {
    const name = formElement.querySelector('#full_name');
    const email = formElement.querySelector('#email');
    const phone = formElement.querySelector('#phone');
    const password = formElement.querySelector('#password');
    const confirmPassword = formElement.querySelector('#confirm_password');

    let isValid = true;
    let errorMessage = '';

    if (name && name.value.trim().length < 2) {
        isValid = false;
        errorMessage = 'Please enter a valid full name.';
    } else if (email && !validateEmail(email.value.trim())) {
        isValid = false;
        errorMessage = 'Please enter a valid email address.';
    } else if (phone && !validatePhone(phone.value.trim())) {
        isValid = false;
        errorMessage = 'Please enter a valid phone number.';
    } else if (password && password.value.length < 6) {
        isValid = false;
        errorMessage = 'Password must be at least 6 characters long.';
    } else if (confirmPassword && password.value !== confirmPassword.value) {
        isValid = false;
        errorMessage = 'Passwords do not match.';
    }

    if (!isValid) {
        showFormAlert(formElement, errorMessage);
    }

    return isValid;
}

function showFormAlert(formElement, text) {
    let existingAlert = formElement.querySelector('.alert-dynamic');
    if (existingAlert) {
        existingAlert.remove();
    }

    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dynamic';
    alertDiv.innerHTML = `<span>${text}</span><button type="button" class="alert-close" onclick="this.parentElement.remove()">✕</button>`;
    formElement.insertBefore(alertDiv, formElement.firstChild);
}
