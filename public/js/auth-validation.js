document.addEventListener('DOMContentLoaded', function() {
    // Login form validation - we'll keep this minimal
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Let form submit naturally - server will handle validation
        });
    }
    
    // Registration form - bypass client-side validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // Let form submit naturally - server will handle all validation
            // No e.preventDefault() means form will submit directly to server
        });
    }
});