<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="registerModalLabel">Tạo tài khoản</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="<?= BASE_URL ?? '' ?>index.php?route=user/register" method="POST">
                    <div class="mb-3">
                        <input placeholder="Email/ Số điện thoại" type="text" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <input placeholder="Mật khẩu" type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <input placeholder="Nhập lại mật khẩu" type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="mb-4">
                        <p class="text-muted small">Bằng việc tạo tài khoản, bạn đồng ý với <a href="#" class="text-decoration-none">Điều khoản dịch vụ</a> và <a href="#" class="text-decoration-none">Chính sách bảo mật</a>.</p>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg auth-btn">Đăng ký</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="mb-0">Đã có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" class="text-decoration-none">Đăng nhập</a></p>
                </div>
            </div>
        </div>
    </div>
</div>