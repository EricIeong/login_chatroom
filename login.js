const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");
const showRegisterLink = document.getElementById("showRegisterLink");
const showLoginLink = document.getElementById("showLoginLink");

showRegisterLink.onclick = function () { showRegister() };
showLoginLink.onclick = function () { showLogin() };

function showRegister() {
    loginForm.style.display = "none";
    registerForm.style.display = "block";
}

function showLogin() {
    registerForm.style.display = "none";
    loginForm.style.display = "block";
}

// Email validation on blur (lost focus)
const regEmail = document.getElementById('regEmail');
if (regEmail) {
    regEmail.addEventListener('blur', function () {
        const email = this.value;
        const errorDiv = document.getElementById('registerError');

        if (email !== '' && !email.endsWith('@connect.hku.hk')) {
            errorDiv.textContent = 'Email must end with @connect.hku.hk';
            errorDiv.style.display = 'block';
        } else {
            // Check if email already exists
            fetch('check.php?email=' + encodeURIComponent(email))
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        errorDiv.textContent = 'Email already exists';
                        errorDiv.style.display = 'block';
                    }
                })
        }
    });
}
// Get the form element inside the registerForm div
const registerFormElement = document.querySelector('#registerForm form');

if (registerFormElement) {
    registerFormElement.addEventListener('submit', function (event) {
        const email = document.getElementById('regEmail').value;
        const password = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regConfirm').value;
        const errorDiv = document.getElementById('registerError');

        let errorMessage = '';

        // Check if fields are empty
        if (email === '' || password === '' || confirm === '') {
            errorMessage = 'All fields are required';
        }
        // Check email domain
        else if (!email.endsWith('@connect.hku.hk')) {
            errorMessage = 'Email must end with @connect.hku.hk';
        }
        // Check if passwords match
        else if (password !== confirm) {
            errorMessage = 'Passwords do not match';
        }

        if (errorMessage !== '') {
            event.preventDefault();
            errorDiv.textContent = errorMessage;
            errorDiv.style.display = 'block';
            return false;
        }

        errorDiv.style.display = 'none';
        return true;
    });
    // Hide error message when user starts typing in any field
    const fields = ['regEmail', 'regPassword', 'regConfirm'];
    fields.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function () {
                const errorDiv = document.getElementById('registerError');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        }
    });
}

// ========== LOGIN FORM VALIDATION ==========

// Get login form elements
const loginFormElement = document.querySelector('#loginForm form');
const loginEmail = document.getElementById('loginEmail');

// Email validation on blur (lost focus)
if (loginEmail) {
    loginEmail.addEventListener('blur', function () {
        const email = this.value;
        const errorDiv = document.getElementById('loginError');

        if (email !== '' && !email.endsWith('@connect.hku.hk')) {
            if (!errorDiv) {
                // Create error div if it doesn't exist
                const newErrorDiv = document.createElement('div');
                newErrorDiv.id = 'loginError';
                newErrorDiv.className = 'error-message';
                newErrorDiv.style.color = 'red';
                newErrorDiv.style.display = 'block';
                newErrorDiv.style.marginBottom = '10px';
                this.parentNode.insertBefore(newErrorDiv, this.nextSibling);
            } else {
                errorDiv.textContent = 'Email must end with @connect.hku.hk';
                errorDiv.style.display = 'block';
            }
        } else if (errorDiv && errorDiv.textContent === 'Email must end with @connect.hku.hk') {
            errorDiv.style.display = 'none';
        }
    });
}

// Login form submit validation
if (loginFormElement) {
    loginFormElement.addEventListener('submit', function (event) {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // Get or create error div
        let errorDiv = document.getElementById('loginError');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'loginError';
            errorDiv.className = 'error-message';
            errorDiv.style.color = 'red';
            errorDiv.style.display = 'none';
            errorDiv.style.marginBottom = '10px';
            loginFormElement.insertBefore(errorDiv, loginFormElement.querySelector('input[type="submit"]'));
        }

        let errorMessage = '';

        // Check if fields are empty
        if (email === '' || password === '') {
            errorMessage = 'All fields are required';
        }
        // Check email domain
        else if (!email.endsWith('@connect.hku.hk')) {
            errorMessage = 'Email must end with @connect.hku.hk';
        }

        if (errorMessage !== '') {
            event.preventDefault();
            errorDiv.textContent = errorMessage;
            errorDiv.style.display = 'block';
            return false;
        }

        errorDiv.style.display = 'none';
        return true;
    });

    // Hide error message when user starts typing
    const loginFields = ['loginEmail', 'loginPassword'];
    loginFields.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function () {
                const errorDiv = document.getElementById('loginError');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        }
    });
}