<?php
// filepath: c:\Users\VY\Downloads\curtaincall\views\layouts\footer.php
?>
<button onclick="topFunction()" id="myBtn" title="Go to top">
    <i class="bi bi-chevron-double-up"></i>
</button>
<script>
    //Get the button
    var mybutton = document.getElementById("myBtn");

    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
</script>
<footer>
    <p>&copy; <?php echo date("Y"); ?> CurtainCall. All Rights Reserved.</p>
</footer>

<?php include 'views/auth/login-modal.php'; ?>
<?php include 'views/auth/register-modal.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Login form AJAX handling
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Clear previous error messages
            document.querySelectorAll('#loginForm .text-danger').forEach(el => {
                el.textContent = '';
            });
            
            // Get form data
            const formData = new FormData(loginForm);
            
            // Send AJAX request
            fetch(loginForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Login successful - redirect
                    window.location.href = data.redirect;
                } else {
                    // Login failed - show errors
                    if (data.errors.general) {
                        // Show general error
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger';
                        alertDiv.textContent = data.errors.general;
                        
                        // Find existing alert or insert at top
                        const existingAlert = loginForm.querySelector('.alert');
                        if (existingAlert) {
                            existingAlert.replaceWith(alertDiv);
                        } else {
                            loginForm.insertBefore(alertDiv, loginForm.firstChild);
                        }
                    }
                    
                    // Show specific field errors
                    if (data.errors.login) {
                        document.getElementById('loginError').textContent = data.errors.login;
                    }
                    if (data.errors.password) {
                        document.getElementById('passwordError').textContent = data.errors.password;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>
</body>

</html>