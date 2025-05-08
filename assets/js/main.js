document.addEventListener('DOMContentLoaded', function() {
  // Initialize dropdowns
  initDropdowns();
  
  // Initialize mobile menu
  initMobileMenu();
  
  // Initialize accordions
  initAccordions();
  
  // Initialize password toggles
  initPasswordToggles();
});

/**
 * Initialize dropdown functionality
 */
function initDropdowns() {
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  
  dropdownToggles.forEach(toggle => {
    const parent = toggle.closest('.dropdown');
    const menu = parent.querySelector('.dropdown-menu');
    
    // Toggle dropdown on click instead of hover for better mobile experience
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Close other open dropdowns
      document.querySelectorAll('.dropdown.active').forEach(activeDropdown => {
        if (activeDropdown !== parent) {
          activeDropdown.classList.remove('active');
          activeDropdown.querySelector('.dropdown-menu').style.display = 'none';
        }
      });
      
      // Toggle current dropdown
      parent.classList.toggle('active');
      menu.style.display = parent.classList.contains('active') ? 'block' : 'none';
    });
  });
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
      document.querySelectorAll('.dropdown.active').forEach(dropdown => {
        dropdown.classList.remove('active');
        dropdown.querySelector('.dropdown-menu').style.display = 'none';
      });
    }
  });
}

/**
 * Initialize mobile menu functionality
 */
function initMobileMenu() {
  const menuToggle = document.querySelector('.mobile-menu-toggle');
  const mobileMenu = document.querySelector('.mobile-menu');
  
  if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', function() {
      this.classList.toggle('active');
      mobileMenu.classList.toggle('active');
      
      // Toggle hamburger to X animation
      const spans = this.querySelectorAll('span');
      if (this.classList.contains('active')) {
        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
      } else {
        spans[0].style.transform = 'none';
        spans[1].style.opacity = '1';
        spans[2].style.transform = 'none';
      }
    });
  }
}

/**
 * Initialize accordion functionality for mobile menu
 */
function initAccordions() {
  const accordionToggles = document.querySelectorAll('.accordion-toggle');
  
  accordionToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      
      const parent = this.closest('.mobile-accordion');
      parent.classList.toggle('active');
      
      const content = this.nextElementSibling;
      if (parent.classList.contains('active')) {
        content.style.display = 'block';
      } else {
        content.style.display = 'none';
      }
    });
  });
}

/**
 * Initialize password visibility toggles
 */
function initPasswordToggles() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const input = this.previousElementSibling;
      const icon = this.querySelector('i');
      
      if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '👁️‍🗨️'; // Closed eye icon
      } else {
        input.type = 'password';
        icon.textContent = '👁️'; // Open eye icon
      }
    });
  });
}

/**
 * Show a notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, info, warning)
 * @param {number} duration - The duration to show the notification in milliseconds
 */
function showNotification(message, type = 'info', duration = 3000) {
  // Check if notification container exists, create if not
  let container = document.querySelector('.notification-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'notification-container';
    document.body.appendChild(container);
  }
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = message;
  
  // Add to container
  container.appendChild(notification);
  
  // Show with animation
  setTimeout(() => {
    notification.classList.add('show');
  }, 10);
  
  // Remove after duration
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, duration);
}

/**
 * Format currency
 * @param {number} amount - The amount to format
 * @param {string} currency - The currency code (default: USD)
 * @returns {string} - Formatted currency string
 */
function formatCurrency(amount, currency = 'USD') {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency
  }).format(amount);
}

/**
 * Get URL parameter by name
 * @param {string} name - Parameter name
 * @returns {string|null} - Parameter value or null
 */
function getUrlParameter(name) {
  name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
  const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
  const results = regex.exec(location.search);
  return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

/**
 * Set cookie
 * @param {string} name - Cookie name
 * @param {string} value - Cookie value
 * @param {number} days - Days until expiration
 */
function setCookie(name, value, days) {
  let expires = '';
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = '; expires=' + date.toUTCString();
  }
  document.cookie = name + '=' + (value || '') + expires + '; path=/';
}

/**
 * Get cookie
 * @param {string} name - Cookie name
 * @returns {string|null} - Cookie value or null
 */
function getCookie(name) {
  const nameEQ = name + '=';
  const ca = document.cookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
  }
  return null;
}

/**
 * Delete cookie
 * @param {string} name - Cookie name
 */
function eraseCookie(name) {
  document.cookie = name + '=; Max-Age=-99999999;';
}