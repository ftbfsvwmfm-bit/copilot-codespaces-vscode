// SIMS - Student Information Management System
// Form Validation & Client-side Functions

// Form Validation Functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[0-9]{10}$/;
    return phoneRegex.test(phone.replace(/\D/g, ''));
}

function validatePassword(password) {
    return password.length >= 6;
}

function validateUsername(username) {
    return username.length >= 3 && /^[a-zA-Z0-9_]+$/.test(username);
}

function validateName(name) {
    return name.trim().length >= 2 && !/[0-9]/.test(name);
}

function validateDate(dateString) {
    const date = new Date(dateString);
    return date instanceof Date && !isNaN(date);
}

// Login Form Validation
function validateLoginForm() {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    let isValid = true;

    // Clear previous errors
    clearErrors();

    // Validate username
    if (!username.value.trim()) {
        showError('username', 'Username is required');
        isValid = false;
    }

    // Validate password
    if (!password.value) {
        showError('password', 'Password is required');
        isValid = false;
    }

    return isValid;
}

// Registration Form Validation
function validateRegistrationForm() {
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const firstName = document.getElementById('firstName');
    const lastName = document.getElementById('lastName');
    const phone = document.getElementById('phone');
    let isValid = true;

    // Clear previous errors
    clearErrors();

    // Validate username
    if (!username.value.trim()) {
        showError('username', 'Username is required');
        isValid = false;
    } else if (!validateUsername(username.value)) {
        showError('username', 'Username must be at least 3 characters and contain only letters, numbers, and underscores');
        isValid = false;
    }

    // Validate email
    if (!email.value.trim()) {
        showError('email', 'Email is required');
        isValid = false;
    } else if (!validateEmail(email.value)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate first name
    if (!firstName.value.trim()) {
        showError('firstName', 'First name is required');
        isValid = false;
    } else if (!validateName(firstName.value)) {
        showError('firstName', 'First name must contain only letters');
        isValid = false;
    }

    // Validate last name
    if (!lastName.value.trim()) {
        showError('lastName', 'Last name is required');
        isValid = false;
    } else if (!validateName(lastName.value)) {
        showError('lastName', 'Last name must contain only letters');
        isValid = false;
    }

    // Validate phone
    if (phone.value && !validatePhone(phone.value)) {
        showError('phone', 'Please enter a valid 10-digit phone number');
        isValid = false;
    }

    // Validate password
    if (!password.value) {
        showError('password', 'Password is required');
        isValid = false;
    } else if (!validatePassword(password.value)) {
        showError('password', 'Password must be at least 6 characters long');
        isValid = false;
    }

    // Validate confirm password
    if (!confirmPassword.value) {
        showError('confirmPassword', 'Please confirm your password');
        isValid = false;
    } else if (password.value !== confirmPassword.value) {
        showError('confirmPassword', 'Passwords do not match');
        isValid = false;
    }

    return isValid;
}

// Student Form Validation
function validateStudentForm() {
    const rollNumber = document.getElementById('rollNumber');
    const className = document.getElementById('class');
    const year = document.getElementById('year');
    const department = document.getElementById('department');
    const dob = document.getElementById('dob');
    let isValid = true;

    clearErrors();

    if (!rollNumber.value.trim()) {
        showError('rollNumber', 'Roll number is required');
        isValid = false;
    }

    if (!className.value.trim()) {
        showError('class', 'Class is required');
        isValid = false;
    }

    if (!year.value) {
        showError('year', 'Year is required');
        isValid = false;
    }

    if (!department.value.trim()) {
        showError('department', 'Department is required');
        isValid = false;
    }

    if (dob.value && !validateDate(dob.value)) {
        showError('dob', 'Please enter a valid date');
        isValid = false;
    }

    return isValid;
}

// Marks Entry Validation
function validateMarksForm() {
    const test1 = document.getElementById('test1');
    const test2 = document.getElementById('test2');
    const assignment = document.getElementById('assignment');
    let isValid = true;

    clearErrors();

    // Validate marks are between 0-100
    if (test1.value && (isNaN(test1.value) || test1.value < 0 || test1.value > 100)) {
        showError('test1', 'Test 1 marks must be between 0 and 100');
        isValid = false;
    }

    if (test2.value && (isNaN(test2.value) || test2.value < 0 || test2.value > 100)) {
        showError('test2', 'Test 2 marks must be between 0 and 100');
        isValid = false;
    }

    if (assignment.value && (isNaN(assignment.value) || assignment.value < 0 || assignment.value > 100)) {
        showError('assignment', 'Assignment marks must be between 0 and 100');
        isValid = false;
    }

    return isValid;
}

// Exam Timetable Validation
function validateTimetableForm() {
    const examDate = document.getElementById('examDate');
    const startTime = document.getElementById('startTime');
    const endTime = document.getElementById('endTime');
    let isValid = true;

    clearErrors();

    if (!examDate.value) {
        showError('examDate', 'Exam date is required');
        isValid = false;
    }

    if (!startTime.value) {
        showError('startTime', 'Start time is required');
        isValid = false;
    }

    if (!endTime.value) {
        showError('endTime', 'End time is required');
        isValid = false;
    }

    if (startTime.value && endTime.value && startTime.value >= endTime.value) {
        showError('endTime', 'End time must be after start time');
        isValid = false;
    }

    return isValid;
}

// Helper Functions
function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    if (field) {
        const errorElement = document.createElement('span');
        errorElement.className = 'error';
        errorElement.textContent = message;

        // Remove existing error message
        const existingError = field.parentElement.querySelector('.error');
        if (existingError) {
            existingError.remove();
        }

        field.parentElement.appendChild(errorElement);
        field.classList.add('error-field');
    }
}

function clearErrors() {
    const errorMessages = document.querySelectorAll('.error');
    errorMessages.forEach(error => error.remove());

    const errorFields = document.querySelectorAll('.error-field');
    errorFields.forEach(field => field.classList.remove('error-field'));
}

// Mark field with error styling
function markError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '#e74c3c';
        field.style.backgroundColor = '#ffe6e6';
    }
}

// Clear field error styling
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '';
        field.style.backgroundColor = '';
    }
}

// Real-time field validation
function setupFieldValidation() {
    const fields = document.querySelectorAll('input, textarea, select');

    fields.forEach(field => {
        field.addEventListener('blur', function () {
            clearFieldError(this.id);
        });

        field.addEventListener('input', function () {
            clearFieldError(this.id);
        });
    });
}

// Confirm action
function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(value);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-IN', options);
}

// Calculate GPA from marks
function calculateGPA(marks) {
    if (marks >= 90) return 4.0;
    if (marks >= 80) return 3.5;
    if (marks >= 70) return 3.0;
    if (marks >= 60) return 2.5;
    if (marks >= 50) return 2.0;
    return 0.0;
}

// Calculate grade from marks
function calculateGrade(marks) {
    if (marks >= 90) return 'O';
    if (marks >= 80) return 'A+';
    if (marks >= 70) return 'A';
    if (marks >= 60) return 'B+';
    if (marks >= 50) return 'B';
    if (marks >= 40) return 'C';
    return 'F';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    setupFieldValidation();

    // Auto-select active menu item
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('.sidebar a');

    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage || item.getAttribute('href').includes(currentPage)) {
            item.classList.add('active');
        }
    });
});

// Show loading spinner
function showLoadingSpinner(show = true) {
    let spinner = document.getElementById('loadingSpinner');
    if (!spinner) {
        spinner = document.createElement('div');
        spinner.id = 'loadingSpinner';
        spinner.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            padding: 2rem;
            border-radius: 8px;
            color: white;
            font-size: 1.2rem;
        `;
        document.body.appendChild(spinner);
    }

    if (show) {
        spinner.innerHTML = '<p>Loading...</p>';
        spinner.style.display = 'block';
    } else {
        spinner.style.display = 'none';
    }
}

// Export functions for use in HTML
window.validateLoginForm = validateLoginForm;
window.validateRegistrationForm = validateRegistrationForm;
window.validateStudentForm = validateStudentForm;
window.validateMarksForm = validateMarksForm;
window.validateTimetableForm = validateTimetableForm;
window.confirmAction = confirmAction;
window.showLoadingSpinner = showLoadingSpinner;
