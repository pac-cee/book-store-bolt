// Authentication page functionality
document.addEventListener('DOMContentLoaded', function() {
  // Password visibility toggle
  const toggleButtons = document.querySelectorAll('.toggle-password');
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const input = this.previousElementSibling;
      const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
      input.setAttribute('type', type);
      this.innerHTML = type === 'password' ? '<i class="icon-eye"></i>' : '<i class="icon-eye-off"></i>';
    });
  });

  // Form validation
  const authForm = document.querySelector('.auth-form');
  if (authForm) {
    authForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Reset validation states
      clearValidationMessages();
      
      // Validate form
      if (validateForm(this)) {
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.classList.add('btn-loading');
        
        // Submit form
        this.submit();
      }
    });
  }

  // Password strength indicator (for registration)
  const passwordInput = document.getElementById('password');
  if (passwordInput) {
    passwordInput.addEventListener('input', function() {
      updatePasswordStrength(this.value);
    });
  }

  // Password confirmation match (for registration)
  const confirmPasswordInput = document.getElementById('confirm-password');
  if (confirmPasswordInput) {
    confirmPasswordInput.addEventListener('input', function() {
      const password = document.getElementById('password').value;
      validatePasswordMatch(password, this.value);
    });
  }
});

// Validate form fields
function validateForm(form) {
  let isValid = true;
  
  // Email validation
  const emailInput = form.querySelector('input[type="email"]');
  if (emailInput && !validateEmail(emailInput.value)) {
    showValidationMessage(emailInput, 'Please enter a valid email address');
    isValid = false;
  }
  
  // Password validation
  const passwordInput = form.querySelector('input[name="password"]');
  if (passwordInput && !validatePassword(passwordInput.value)) {
    showValidationMessage(passwordInput, 'Password must be at least 8 characters');
    isValid = false;
  }
  
  // Confirm password validation
  const confirmPasswordInput = form.querySelector('input[name="confirm_password"]');
  if (confirmPasswordInput && confirmPasswordInput.value !== passwordInput.value) {
    showValidationMessage(confirmPasswordInput, 'Passwords do not match');
    isValid = false;
  }
  
  // Terms checkbox validation (for registration)
  const termsCheckbox = form.querySelector('input[name="terms"]');
  if (termsCheckbox && !termsCheckbox.checked) {
    showValidationMessage(termsCheckbox, 'You must accept the terms and conditions');
    isValid = false;
  }
  
  return isValid;
}

// Email validation helper
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Password validation helper
function validatePassword(password) {
  return password.length >= 8;
}

// Show validation message
function showValidationMessage(input, message) {
  const container = input.parentElement;
  let messageElement = container.querySelector('.validation-message');
  
  if (!messageElement) {
    messageElement = document.createElement('div');
    messageElement.className = 'validation-message';
    container.appendChild(messageElement);
  }
  
  messageElement.textContent = message;
  input.classList.add('invalid');
}

// Clear validation messages
function clearValidationMessages() {
  const messages = document.querySelectorAll('.validation-message');
  messages.forEach(message => message.remove());
  
  const invalidInputs = document.querySelectorAll('.invalid');
  invalidInputs.forEach(input => input.classList.remove('invalid'));
}

// Password strength indicator
function updatePasswordStrength(password) {
  const strengthIndicator = document.querySelector('.password-strength');
  if (!strengthIndicator) return;
  
  let strength = 0;
  
  // Length check
  if (password.length >= 8) strength++;
  
  // Contains number
  if (/\d/.test(password)) strength++;
  
  // Contains letter
  if (/[a-zA-Z]/.test(password)) strength++;
  
  // Contains special character
  if (/[^A-Za-z0-9]/.test(password)) strength++;
  
  // Update indicator
  strengthIndicator.className = 'password-strength';
  switch(strength) {
    case 0:
    case 1:
      strengthIndicator.classList.add('weak');
      strengthIndicator.textContent = 'Weak';
      break;
    case 2:
    case 3:
      strengthIndicator.classList.add('medium');
      strengthIndicator.textContent = 'Medium';
      break;
    case 4:
      strengthIndicator.classList.add('strong');
      strengthIndicator.textContent = 'Strong';
      break;
  }
}

// Validate password match
function validatePasswordMatch(password, confirmPassword) {
  const confirmInput = document.getElementById('confirm-password');
  const container = confirmInput.parentElement;
  
  if (password === confirmPassword) {
    container.classList.remove('invalid');
    const message = container.querySelector('.validation-message');
    if (message) message.remove();
  } else {
    showValidationMessage(confirmInput, 'Passwords do not match');
  }
}