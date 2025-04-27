// Hady's part

// Login form validation
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('LoginForm').addEventListener('submit', function (event) {

        event.preventDefault();


        const emailError = document.getElementById('emailError');
        const passwordError = document.getElementById('passwordError');
        const emailInput = document.getElementById('Email');
        const passwordInput = document.getElementById('Password');

        // clear any previous erros
        emailError.textContent = '';
        passwordError.textContent = '';
        emailInput.classList.remove('input-error');
        passwordInput.classList.remove('input-error');

        let isValid = true;

        // Validate email
        const emailValue = emailInput.value.trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailValue) {
            emailError.textContent = 'Email is required';
            emailInput.classList.add('input-error');
            isValid = false;
        } else if (!emailPattern.test(emailValue)) {
            emailError.textContent = 'Please enter a valid email address';
            emailInput.classList.add('input-error');
            isValid = false;
        }

        // Validate password
        const passwordValue = passwordInput.value.trim();
        if (!passwordValue) {
            passwordError.textContent = 'Password is required';
            passwordInput.classList.add('input-error');
            isValid = false;
        } else if (passwordValue.length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters';
            passwordInput.classList.add('input-error');
            isValid = false;
        }

        // If everything is valid
        if (isValid) {
            document.getElementById('LoginForm').submit()
        }

    });
});

// Registration form validation
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('RegisterForm').addEventListener('submit', function (event) {

        event.preventDefault();

        const emailError = document.getElementById('emailError');
        const nameError = document.getElementById('nameError');
        const passwordError = document.getElementById('passwordError');
        const numberError = document.getElementById('numberError');
        const fullNameInput = document.getElementById('Name');
        const emailInput = document.getElementById('Email');
        const numberInput = document.getElementById('Phone');
        const passwordInput = document.getElementById('Password');

        // Clear any previous error messages
        emailError.textContent = '';
        passwordError.textContent = '';
        numberError.textContent = '';
        nameError.textContent = '';
        fullNameInput.classList.remove('input-error');
        emailInput.classList.remove('input-error');
        numberInput.classList.remove('input-error');
        passwordInput.classList.remove('input-error');

        let isValid = true;

        // Validate Full Name
        const fullNameValue = fullNameInput.value.trim();
        if (!fullNameValue) {
            fullNameInput.classList.add('input-error');
            nameError.textContent = 'Name is required';
            isValid = false;
        }

        // Validate email
        const emailValue = emailInput.value.trim();
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailValue) {
            emailError.textContent = 'Email is required';
            emailInput.classList.add('input-error');
            isValid = false;
        } else if (!emailPattern.test(emailValue)) {
            emailError.textContent = 'Please enter a valid email address';
            emailInput.classList.add('input-error');
            isValid = false;
        }

        // Validate phone number 
        const numberValue = numberInput.value.trim();
        const numberPattern = /^[0-9]{8}$/;
        if (!numberValue) {
            numberError.textContent = 'Phone number is required';
            numberInput.classList.add('input-error');
            isValid = false;
        } else if (!numberPattern.test(numberValue)) {
            numberError.textContent = 'Phone number must be exactly 8 digits';
            numberInput.classList.add('input-error');
            isValid = false;
        }

        // Validate password
        const passwordValue = passwordInput.value.trim();
        const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;
        if (!passwordValue) {
            passwordError.textContent = 'Password is required';
            passwordInput.classList.add('input-error');
            isValid = false;
        } else if (passwordValue.length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters';
            passwordInput.classList.add('input-error');
            isValid = false;
        } else if (!passwordPattern.test(passwordValue)) {
            passwordError.textContent = 'Password must contain both letters and numbers';
            passwordInput.classList.add('input-error');
            isValid = false;
        }

        // Everything is valid
        if (isValid) {
            document.getElementById('RegisterForm').submit()
        }
    });
});

// Change password form validation
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('ChangePasswordForm').addEventListener('submit', function (event) {

        event.preventDefault();
        isValid = true;

        const oldPasswordInput = document.getElementById('OldPassword');
        const oldPasswordError = document.getElementById('oldPasswordError');
        const newPasswordInput = document.getElementById('NewPassword');
        const newPasswordError = document.getElementById('newPasswordError');

        // Reset
        oldPasswordError.textContent = '';
        oldPasswordInput.classList.remove('input-error');
        newPasswordError.textContent = '';
        newPasswordInput.classList.remove('input-error');

        // Validate old password
        const oldPasswordValue = oldPasswordInput.value.trim();
        const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;
        if (!oldPasswordValue) {
            oldPasswordError.textContent = 'Password is required';
            oldPasswordInput.classList.add('input-error');
            isValid = false;
        } else if (oldPasswordValue.length < 8) {
            oldPasswordError.textContent = 'Password must be at least 8 characters';
            oldPasswordInput.classList.add('input-error');
            isValid = false;
        } else if (!passwordPattern.test(oldPasswordValue)) {
            oldPasswordError.textContent = 'Password must contain both letters and numbers';
            oldPasswordInput.classList.add('input-error');
            isValid = false;
        }

        // Validate new password
        const newPasswordValue = newPasswordInput.value.trim();
        if (!newPasswordValue) {
            newPasswordError.textContent = 'Password is required';
            newPasswordInput.classList.add('input-error');
            isValid = false;
        } else if (newPasswordValue.length < 8) {
            newPasswordError.textContent = 'Password must be at least 8 characters';
            newPasswordInput.classList.add('input-error');
            isValid = false;
        } else if (!passwordPattern.test(newPasswordValue)) {
            newPasswordError.textContent = 'Password must contain both letters and numbers';
            newPasswordInput.classList.add('input-error');
            isValid = false;
        }

        // Everything is valid
        if (isValid) {
            document.getElementById('ChangePasswordForm').submit()
        }

    })

})

// toggle the navigation bar in the header
document.addEventListener("DOMContentLoaded", function(){
    document.getElementById('bars-btn').addEventListener('click', function () {
        document.getElementById('dropdown-menu').classList.toggle('active');
    });
})


// Amir's part
