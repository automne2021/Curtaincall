document.addEventListener('DOMContentLoaded', function() {
            // Find all alert notifications
            const alerts = document.querySelectorAll('.alert');

            // Set timeout for each alert
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    // Create fade out effect
                    alert.style.transition = 'opacity 1s';
                    alert.style.opacity = '0';

                    // Remove the element after the fade completes
                    setTimeout(function() {
                        // Use Bootstrap's alert dismiss method if available
                        if (typeof bootstrap !== 'undefined') {
                            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                            bsAlert.close();
                        } else {
                            alert.remove();
                        }
                    }, 1000);
                }, 3000);
            });
});