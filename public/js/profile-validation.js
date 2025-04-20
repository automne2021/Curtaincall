document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements for password change form
    const passwordChangeForm = document.getElementById('change-password')?.querySelector('form');
    if (!passwordChangeForm) return;
    
    const currentPasswordInput = passwordChangeForm.querySelector('[name="current_password"]');
    const newPasswordInput = passwordChangeForm.querySelector('[name="new_password"]');
    const confirmPasswordInput = passwordChangeForm.querySelector('[name="confirm_password"]');
    
    // Regular expressions and validation rules for password
    const passwordRules = {
        length: /.{8,}/,
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        number: /[0-9]/,
        special: /[!@#$%^&*(),.?":{}|<>]/
    };
    
    /**
     * Validate new password against security rules
     */
    function validateNewPassword() {
        const password = newPasswordInput.value;
        const errorElement = document.getElementById('new_password_error') || createErrorElement(newPasswordInput, 'new_password_error');
        
        errorElement.textContent = '';
        
        if (password.length === 0) {
            errorElement.textContent = 'Mật khẩu không được để trống';
            return false;
        }
        
        if (!passwordRules.length.test(password)) {
            errorElement.textContent = 'Mật khẩu phải có ít nhất 8 ký tự';
            return false;
        }
        
        // Count different character types
        let typeCount = 0;
        if (passwordRules.lowercase.test(password)) typeCount++;
        if (passwordRules.uppercase.test(password)) typeCount++;
        if (passwordRules.number.test(password)) typeCount++;
        if (passwordRules.special.test(password)) typeCount++;
        
        if (typeCount < 3) {
            errorElement.textContent = 'Mật khẩu phải chứa ít nhất 3 loại ký tự (chữ thường, chữ hoa, số, ký tự đặc biệt)';
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate confirm password matches new password
     */
    function validateConfirmPassword() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const errorElement = document.getElementById('confirm_password_error') || createErrorElement(confirmPasswordInput, 'confirm_password_error');
        
        errorElement.textContent = '';
        
        if (confirmPassword.length === 0) {
            errorElement.textContent = 'Vui lòng nhập lại mật khẩu';
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            errorElement.textContent = 'Mật khẩu không khớp';
            return false;
        }
        
        return true;
    }
    
    /**
     * Helper function to create error message elements if they don't exist
     */
    function createErrorElement(inputElement, id) {
        const errorDiv = document.createElement('div');
        errorDiv.id = id;
        errorDiv.className = 'text-danger';
        errorDiv.style.fontSize = '0.875em';
        inputElement.insertAdjacentElement('afterend', errorDiv);
        return errorDiv;
    }
    
    /**
     * Add password strength indicator
     */
    function addPasswordStrengthIndicator() {
        // Create password strength indicator elements
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength mt-2';
        strengthContainer.innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="strength-text text-muted mt-1">Độ mạnh: Chưa nhập mật khẩu</small>
        `;
        
        // Insert after new password input
        newPasswordInput.parentNode.insertBefore(strengthContainer, newPasswordInput.nextSibling);
        
        const progressBar = strengthContainer.querySelector('.progress-bar');
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        // Update strength indicator on input
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                progressBar.style.width = '0%';
                progressBar.className = 'progress-bar';
                strengthText.textContent = 'Độ mạnh: Chưa nhập mật khẩu';
                strengthText.className = 'strength-text text-muted mt-1';
                return;
            }
            
            // Calculate password strength
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 25;
            
            // Character type checks
            if (passwordRules.lowercase.test(password)) strength += 15;
            if (passwordRules.uppercase.test(password)) strength += 15;
            if (passwordRules.number.test(password)) strength += 15;
            if (passwordRules.special.test(password)) strength += 30;
            
            // Update progress bar
            progressBar.style.width = `${strength}%`;
            
            if (strength < 25) {
                progressBar.className = 'progress-bar bg-danger';
                strengthText.textContent = 'Độ mạnh: Rất yếu';
                strengthText.className = 'strength-text text-danger mt-1';
            } else if (strength < 50) {
                progressBar.className = 'progress-bar bg-warning';
                strengthText.textContent = 'Độ mạnh: Yếu';
                strengthText.className = 'strength-text text-warning mt-1';
            } else if (strength < 75) {
                progressBar.className = 'progress-bar bg-info';
                strengthText.textContent = 'Độ mạnh: Trung bình';
                strengthText.className = 'strength-text text-info mt-1';
            } else {
                progressBar.className = 'progress-bar bg-success';
                strengthText.textContent = 'Độ mạnh: Mạnh';
                strengthText.className = 'strength-text text-success mt-1';
            }
        });
    }
    
    /**
     * Add event listeners for validation
     */
    function setupValidation() {
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', validateNewPassword);
            newPasswordInput.addEventListener('blur', validateNewPassword);
        }
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validateConfirmPassword);
            confirmPasswordInput.addEventListener('blur', validateConfirmPassword);
        }
        
        // Form submission
        passwordChangeForm.addEventListener('submit', function(e) {
            const isNewPasswordValid = validateNewPassword();
            const isConfirmPasswordValid = validateConfirmPassword();
            
            if (!isNewPasswordValid || !isConfirmPasswordValid) {
                e.preventDefault();
            }
        });
    }
    
    // Initialize validation and password strength indicator
    setupValidation();
    addPasswordStrengthIndicator();
});