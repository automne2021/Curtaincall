/**
 * Form validation script for registration form
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements for registration form using context-aware queries
    const registerForm = document.getElementById('registerForm');
    if (!registerForm) return;
    
    const usernameInput = registerForm.querySelector('[name="username"]');
    const emailInput = registerForm.querySelector('[name="email"]');
    const passwordInput = registerForm.querySelector('[name="password"]');
    const confirmPasswordInput = registerForm.querySelector('[name="confirm_password"]');
    
    // Get error elements within the form context
    const usernameError = registerForm.querySelector('#usernameError');
    const emailError = registerForm.querySelector('#emailError');
    const passwordError = registerForm.querySelector('#passwordError');
    const confirmError = registerForm.querySelector('#confirmError');
    
    // Regular expressions for validation
    const patterns = {
        username: /^[a-zA-Z0-9_]{5,}$/,
        email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
        password: {
            length: /.{8,}/,
            lowercase: /[a-z]/,
            uppercase: /[A-Z]/,
            number: /[0-9]/,
            special: /[!@#$%^&*(),.?":{}|<>]/
        }
    };
    
    // Debounce function to limit API calls
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
    
    /**
     * Validate registration form fields
     */
    function validateRegistrationForm() {
        // Username validation
        if (usernameInput) {
            usernameInput.addEventListener('input', debounce(function() {
                const username = this.value.trim();
                
                // Reset error message
                usernameError.textContent = '';
                
                if (username.length === 0) {
                    usernameError.textContent = 'Tên đăng nhập không được để trống';
                    return false;
                }
                
                if (username.length < 5) {
                    usernameError.textContent = 'Tên đăng nhập phải có ít nhất 5 ký tự';
                    return false;
                }
                
                if (!patterns.username.test(username)) {
                    usernameError.textContent = 'Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới';
                    return false;
                }
                
                // Check if username already exists via AJAX
                checkUsernameAvailability(username, usernameError);
                
                return true;
            }, 500));
        }
        
        // Email validation
        if (emailInput) {
            emailInput.addEventListener('input', debounce(function() {
                const email = this.value.trim();
                
                // Reset error message
                emailError.textContent = '';
                
                if (email.length === 0) {
                    emailError.textContent = 'Email không được để trống';
                    return false;
                }
                
                if (!patterns.email.test(email)) {
                    emailError.textContent = 'Email không hợp lệ';
                    return false;
                }
                
                // Check if email already exists via AJAX
                checkEmailAvailability(email, emailError);
                
                return true;
            }, 500));
        }
        
        // Password validation
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Reset error message
                passwordError.textContent = '';
                
                if (password.length === 0) {
                    passwordError.textContent = 'Mật khẩu không được để trống';
                    return false;
                }
                
                if (!patterns.password.length.test(password)) {
                    passwordError.textContent = 'Mật khẩu phải có ít nhất 8 ký tự';
                    return false;
                }
                
                // Count different character types
                let typeCount = 0;
                if (patterns.password.lowercase.test(password)) typeCount++;
                if (patterns.password.uppercase.test(password)) typeCount++;
                if (patterns.password.number.test(password)) typeCount++;
                if (patterns.password.special.test(password)) typeCount++;
                
                if (typeCount < 3) {
                    passwordError.textContent = 'Mật khẩu phải chứa ít nhất 3 loại ký tự (chữ thường, chữ hoa, số, ký tự đặc biệt)';
                    return false;
                }
                
                // If confirm password is already filled, check if they match
                if (confirmPasswordInput.value.length > 0) {
                    validateConfirmPassword();
                }
                
                return true;
            });
        }
        
        // Confirm password validation
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validateConfirmPassword);
        }
        
        // Form submission
        registerForm.addEventListener('submit', function(e) {
            const username = usernameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Validate all fields again
            let isValid = true;
            
            // Username validation
            if (username.length < 5 || !patterns.username.test(username)) {
                usernameError.textContent = 'Tên đăng nhập không hợp lệ';
                isValid = false;
            }
            
            // Email validation
            if (!patterns.email.test(email)) {
                emailError.textContent = 'Email không hợp lệ';
                isValid = false;
            }
            
            // Password validation
            if (!patterns.password.length.test(password)) {
                passwordError.textContent = 'Mật khẩu phải có ít nhất 8 ký tự';
                isValid = false;
            } else {
                // Count different character types
                let typeCount = 0;
                if (patterns.password.lowercase.test(password)) typeCount++;
                if (patterns.password.uppercase.test(password)) typeCount++;
                if (patterns.password.number.test(password)) typeCount++;
                if (patterns.password.special.test(password)) typeCount++;
                
                if (typeCount < 3) {
                    passwordError.textContent = 'Mật khẩu phải chứa ít nhất 3 loại ký tự (chữ thường, chữ hoa, số, ký tự đặc biệt)';
                    isValid = false;
                }
            }
            
            // Confirm password validation
            if (password !== confirmPassword) {
                confirmError.textContent = 'Mật khẩu không khớp';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    /**
     * Validate confirm password
     */
    function validateConfirmPassword() {
        if (!confirmPasswordInput || !passwordInput) return false;
    
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
    
        confirmError.textContent = '';
    
        if (confirmPassword.length === 0) {
            confirmError.textContent = 'Vui lòng nhập lại mật khẩu';
            return false;
        }
    
        if (password !== confirmPassword) {
            confirmError.textContent = 'Mật khẩu không khớp';
            return false;
        }
    
        return true;
    }
    
    /**
     * Check username availability via AJAX
     */
    function checkUsernameAvailability(username, errorElement) {
        fetch(`index.php?route=user/checkUsername&username=${encodeURIComponent(username)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    errorElement.textContent = 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác';
                }
            })
            .catch(error => {
                console.error('Error checking username:', error);
            });
    }
    
    /**
     * Check email availability via AJAX
     */
    function checkEmailAvailability(email, errorElement) {
        fetch(`index.php?route=user/checkEmail&email=${encodeURIComponent(email)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    errorElement.textContent = 'Email này đã được sử dụng';
                }
            })
            .catch(error => {
                console.error('Error checking email:', error);
            });
    }
    
    /**
     * Show password strength indicator
     */
    function addPasswordStrengthIndicator() {
        // Create password strength indicator elements
        const strengthContainer = document.createElement('div');
        strengthContainer.className = 'password-strength mt-1';
        strengthContainer.innerHTML = `
            <div class="progress" style="height: 5px;">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small class="strength-text text-muted mt-1">Độ mạnh: Chưa nhập mật khẩu</small>
        `;
        
        // Insert after password input
        passwordInput.parentNode.insertBefore(strengthContainer, passwordInput.nextSibling);
        
        const progressBar = strengthContainer.querySelector('.progress-bar');
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        // Update strength indicator on input
        passwordInput.addEventListener('input', function() {
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
            if (patterns.password.lowercase.test(password)) strength += 15;
            if (patterns.password.uppercase.test(password)) strength += 15;
            if (patterns.password.number.test(password)) strength += 15;
            if (patterns.password.special.test(password)) strength += 30;
            
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
    
    // Initialize registration form validation
    validateRegistrationForm();
    
    // Add password strength indicator
    addPasswordStrengthIndicator();
});