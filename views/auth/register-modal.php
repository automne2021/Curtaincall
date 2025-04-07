<?php
// filepath: c:\xampp\htdocs\Curtaincall\views\auth\register-modal.php
$register_errors = $_SESSION['register_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
// Clear session data after use
unset($_SESSION['register_errors'], $_SESSION['form_data']);
?>
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="registerModalLabel">Tạo tài khoản</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if (!empty($register_errors['general'])): ?>
                    <div class="alert alert-danger"><?= $register_errors['general'] ?></div>
                <?php endif; ?>

                <form id="registerForm" action="<?= BASE_URL ?>index.php?route=user/register" method="POST">
                    <div class="mb-3">
                        <input placeholder="Tên đăng nhập" type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($form_data['username'] ?? '') ?>">
                        <?php if (!empty($register_errors['username'])): ?>
                            <small class="text-danger"><?= $register_errors['username'] ?></small>
                        <?php endif; ?>
                        <small id="usernameError" class="text-danger"></small>
                    </div>
                    
                    <div class="mb-3">
                        <input placeholder="Email" type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                        <?php if (!empty($register_errors['email'])): ?>
                            <small class="text-danger"><?= $register_errors['email'] ?></small>
                        <?php endif; ?>
                        <small id="emailError" class="text-danger"></small>
                    </div>
                    
                    <div class="mb-3">
                        <input placeholder="Mật khẩu" type="password" class="form-control" id="password" name="password">
                        <?php if (!empty($register_errors['password'])): ?>
                            <small class="text-danger"><?= $register_errors['password'] ?></small>
                        <?php endif; ?>
                        <small id="passwordError" class="text-danger"></small>
                    </div>
                    
                    <div class="mb-3">
                        <input placeholder="Nhập lại mật khẩu" type="password" class="form-control" id="confirm_password" name="confirm_password">
                        <?php if (!empty($register_errors['confirm_password'])): ?>
                            <small class="text-danger"><?= $register_errors['confirm_password'] ?></small>
                        <?php endif; ?>
                        <small id="confirmError" class="text-danger"></small>
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