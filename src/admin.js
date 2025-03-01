// This file handles the admin panel functionality

// Import admin-specific styles
import './styles/admin.scss';

// Simple initialization for the admin panel
document.addEventListener('DOMContentLoaded', function() {
  const imagineSettingsForm = document.getElementById('imagine-settings-form');
  
  if (imagineSettingsForm) {
    imagineSettingsForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Form submission logic would go here
      console.log('Settings form submitted');
      
      // Show success message
      const messageEl = document.createElement('div');
      messageEl.className = 'notice notice-success is-dismissible';
      messageEl.innerHTML = '<p>Settings saved successfully!</p>';
      
      const heading = document.querySelector('.wrap h1');
      heading.insertAdjacentElement('afterend', messageEl);
    });
  }
  
  // Initialize any admin UI components
  initAdminComponents();

  // Make admin notices dismissible
  const notices = document.querySelectorAll('.imagine-notice .notice-dismiss');
  notices.forEach(notice => {
    notice.addEventListener('click', function() {
      const noticeEl = this.parentElement;
      noticeEl.style.opacity = 0;
      setTimeout(() => {
        noticeEl.style.display = 'none';
      }, 300);
    });
  });
});

function initAdminComponents() {
  // Initialize any additional admin components or UI elements
  console.log('Imagine admin panel initialized');
}
