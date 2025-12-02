import './bootstrap';
/*
  Add custom scripts here
*/

// Password toggle functionality
function initPasswordToggle() {
    document.querySelectorAll('.form-password-toggle').forEach(function(formGroup) {
        const passwordInput = formGroup.querySelector('input[type="password"], input[type="text"]');
        const toggleIcon = formGroup.querySelector('.input-group-text i');
        const toggleButton = formGroup.querySelector('.input-group-text');

        if (passwordInput && toggleIcon && toggleButton) {
            // Remove any existing listeners
            const newToggleButton = toggleButton.cloneNode(true);
            toggleButton.parentNode.replaceChild(newToggleButton, toggleButton);

            newToggleButton.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = newToggleButton.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bx-hide');
                    icon.classList.add('bx-show');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bx-show');
                    icon.classList.add('bx-hide');
                }
            });
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initPasswordToggle);

// Re-initialize after Livewire updates (for dynamic content)
document.addEventListener('livewire:navigated', initPasswordToggle);
document.addEventListener('livewire:load', initPasswordToggle);
