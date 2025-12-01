// Form Validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9]{10,11}$/;
    return re.test(phone.replace(/[\s-]/g, ''));
}

function validateRequired(value) {
    return value.trim().length > 0;
}

function showError(inputId, message) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    let errorElement = formGroup.querySelector('.form-error');
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'form-error';
        formGroup.appendChild(errorElement);
    }

    errorElement.textContent = message;
    input.style.borderColor = '#b91c1c';
}

function clearError(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const formGroup = input.closest('.form-group');
    if (!formGroup) return;

    const errorElement = formGroup.querySelector('.form-error');
    if (errorElement) {
        errorElement.textContent = '';
    }

    input.style.borderColor = '';
}

function clearAllErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    const errors = form.querySelectorAll('.form-error');
    errors.forEach(error => error.textContent = '');

    const inputs = form.querySelectorAll('.input');
    inputs.forEach(input => input.style.borderColor = '');
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function () {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value && !validateEmail(this.value)) {
                showError(this.id, 'Please enter a valid email address');
            } else {
                clearError(this.id);
            }
        });
    });

    // Phone validation
    const phoneInputs = document.querySelectorAll('input[name="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (this.value && !validatePhone(this.value)) {
                showError(this.id, 'Please enter a valid phone number');
            } else {
                clearError(this.id);
            }
        });
    });

    // Required field validation
    const requiredInputs = document.querySelectorAll('[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function () {
            if (!validateRequired(this.value)) {
                showError(this.id, 'This field is required');
            } else {
                clearError(this.id);
            }
        });
    });
});
