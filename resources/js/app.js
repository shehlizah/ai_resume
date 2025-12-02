import './bootstrap';
/*
  Add custom scripts here
*/

// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle password toggle for all password fields
    document.querySelectorAll('.form-password-toggle').forEach(function(formGroup) {
        const passwordInput = formGroup.querySelector('input[type="password"]');
        const toggleIcon = formGroup.querySelector('.input-group-text i');
        
        if (passwordInput && toggleIcon) {
            toggleIcon.parentElement.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleIcon.classList.remove('bx-hide');
                    toggleIcon.classList.add('bx-show');
                } else {
                    passwordInput.type = 'password';
                    toggleIcon.classList.remove('bx-show');
                    toggleIcon.classList.add('bx-hide');
                }
            });
        }
    });
});
