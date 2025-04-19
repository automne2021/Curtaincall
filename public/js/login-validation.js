/**
 * Form validation script for login form
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements for login form
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    // Using form-scoped queries to avoid ID conflicts
    const loginInput = loginForm.querySelector('[name="login"]');
    const loginPasswordInput = loginForm.querySelector('[name="password"]');
    const loginError = loginForm.querySelector('#loginError');
    const passwordError = loginForm.querySelector('#passwordError');
    
    /**
     * Validate login form
     */
    function validateLoginForm() {        
        // Login input validation
        if (loginInput) {
            loginInput.addEventListener('input', function() {
                const login = this.value.trim();
                
                // Reset error message
                loginError.textContent = '';
                
                if (login.length === 0) {
                    loginError.textContent = 'Vui lòng nhập tên đăng nhập hoặc email';
                    return false;
                }
                
                return true;
            });
        }
        
        // Password validation - simplified to just check if empty
        if (loginPasswordInput) {
            loginPasswordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Reset error message
                passwordError.textContent = '';
                
                if (password.length === 0) {
                    passwordError.textContent = 'Vui lòng nhập mật khẩu';
                    return false;
                }
                
                return true;
            });
        }
        
        // Form submission validation
        loginForm.addEventListener('submit', function(e) {
            const login = loginInput.value.trim();
            const password = loginPasswordInput.value;
            
            let isValid = true;
            
            if (login.length === 0) {
                loginError.textContent = 'Vui lòng nhập tên đăng nhập hoặc email';
                isValid = false;
            }
            
            if (password.length === 0) {
                passwordError.textContent = 'Vui lòng nhập mật khẩu';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Initialize login form validation
    validateLoginForm();
});