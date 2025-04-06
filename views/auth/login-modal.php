<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="loginModalLabel">Đăng nhập</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= BASE_URL ?? '' ?>index.php?route=user/login" method="POST">
                    <div class="mb-3">
                        <input placeholder="Email/ Số điện thoại" type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <input placeholder="Mật khẩu" type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <div class="d-flex justify-content-end mb-2">
                            <a href="<?= BASE_URL ?>index.php?route=user/forgot-password" class="text-primary text-decoration-none small">
                                Quên mật khẩu?
                            </a>
                        </div>
                        <button type="submit" class="btn btn-lg auth-btn">Đăng nhập</button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="position-relative my-4">
                    <hr>
                    <p class="position-absolute top-0 start-50 translate-middle bg-white px-2 text-muted small">hoặc</p>
                </div>

                <!-- Social Login Buttons -->

                <div class="d-grid gap-2">
                    <a href="<?= BASE_URL ?>index.php?route=user/facebook-login" class="btn social-btn facebook-btn">
                        <i class="bi bi-facebook text-primary me-2"></i> Tiếp tục với Facebook
                    </a>
                    <a href="<?= BASE_URL ?>index.php?route=user/google-login" class="btn social-btn google-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48" class="me-2">
                            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                        </svg> Tiếp tục với Google
                    </a>
                </div>

                <div class="text-center mt-4">
                    <p class="mb-0">Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal" class="text-decoration-none">Đăng ký</a></p>
                </div>
            </div>
        </div>
    </div>
</div>