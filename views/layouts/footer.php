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
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- About section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4">Về Curtain Call</h5>
                <p class="small">Curtain Call là nền tảng đặt vé kịch trực tuyến hàng đầu Việt Nam, cung cấp các vở kịch chất lượng cao từ nhiều nhà hát uy tín trên toàn quốc.</p>
                <div class="social-links mt-4">
                    <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-youtube fs-5"></i></a>
                </div>
            </div>
            
            <!-- Quick links section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4">Liên kết nhanh</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Trang chủ</a></li>
                    <li class="mb-2"><a href="index.php?route=play" class="text-white text-decoration-none">Vở diễn</a></li>
                    <li class="mb-2"><a href="index.php?route=about" class="text-white text-decoration-none">Về chúng tôi</a></li>
                    <li class="mb-2"><a href="index.php?route=contact" class="text-white text-decoration-none">Liên hệ</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="mb-2"><a href="index.php?route=booking/history" class="text-white text-decoration-none">Lịch sử đặt vé</a></li>
                        <li class="mb-2"><a href="index.php?route=user/profile" class="text-white text-decoration-none">Tài khoản</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="index.php?route=user/login" class="text-white text-decoration-none">Đăng nhập</a></li>
                        <li class="mb-2"><a href="index.php?route=user/register" class="text-white text-decoration-none">Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Contact info -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4">Liên hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        267 Lý Thường Kiệt, Phường 15, Quận 11, Hồ Chí Minh
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <a href="tel:+84123456789" class="text-white text-decoration-none">+84 123 456 789</a>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-envelope-fill me-2"></i>
                        <a href="mailto:info@curtaincall.com" class="text-white text-decoration-none">info@curtaincall.com</a>
                    </li>
                    <li>
                        <i class="bi bi-clock-fill me-2"></i>
                        Thứ 2 - Chủ nhật: 8h - 22h
                    </li>
                </ul>
            </div>
            
            <!-- Newsletter section -->
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4">Đăng ký nhận tin</h5>
                <p class="small">Đăng ký để nhận thông báo về các vở diễn mới và ưu đãi đặc biệt.</p>
                <form>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email của bạn" aria-label="Email của bạn">
                        <button class="btn btn-primary" type="button">Đăng ký</button>
                    </div>
                </form>
                <p class="small mt-4">
                    <i class="bi bi-shield-check me-2"></i> 
                    Thanh toán an toàn & bảo mật
                </p>
                <div class="payment-methods mt-3">
                    <img src="public/images/payments/visa.png" alt="Visa" height="24" class="me-2">
                    <img src="public/images/payments/mastercard.png" alt="Mastercard" height="24" class="me-2">
                    <img src="public/images/payments/momo.png" alt="MoMo" height="24" class="me-2">
                    <img src="public/images/payments/zalopay.png" alt="ZaloPay" height="24">
                </div>
            </div>
        </div>
        
        <!-- Copyright and policies row -->
        <div class="row mt-4 pt-4 border-top border-secondary">
            <div class="col-md-6">
                <p class="small mb-md-0">&copy; <?= date('Y') ?> Curtain Call. Tất cả các quyền được bảo lưu.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-white text-decoration-none small me-3">Điều khoản sử dụng</a>
                <a href="#" class="text-white text-decoration-none small me-3">Chính sách bảo mật</a>
                <a href="#" class="text-white text-decoration-none small">FAQ</a>
            </div>
        </div>
    </div>
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