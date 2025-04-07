<?php
$user = $_SESSION['user'] ?? null;
$password_errors = $_SESSION['password_errors'] ?? [];
unset($_SESSION['password_errors']);
?>

<main class="container mt-5 mb-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php
                    $avatarUrl = isset($user['avatar']) && $user['avatar'] ? $user['avatar'] : BASE_URL . 'public/images/avatars/default.png'; 
                    ?>
                    <img src="<?= $avatarUrl ?>" alt="User Avatar" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="mb-0"><?= htmlspecialchars($user['username']) ?></h5>
                    <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-person me-2"></i> Thông tin cá nhân
                    </a>
                    <a href="#change-password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-lock me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="index.php?route=booking/history" class="list-group-item list-group-item-action">
                        <i class="bi bi-ticket-perforated me-2"></i> Lịch sử đặt vé
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Information Tab -->
                <div class="tab-pane fade show active" id="profile-info">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <form action="index.php?route=user/updateProfile" method="POST" enctype="multipart/form-data">
                                <!-- Username (readonly) -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                                    <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                                </div>
                                
                                <!-- Email (readonly) -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                                
                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>">
                                </div>
                                
                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                                </div>
                                
                                <!-- Address -->
                                <div class="mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                </div>
                                
                                <!-- Avatar -->
                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                    <div class="form-text">Chấp nhận các định dạng: JPG, JPEG, PNG, GIF. Kích thước tối đa: 2MB.</div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="change-password">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($password_errors['general'])): ?>
                                <div class="alert alert-danger"><?= $password_errors['general'] ?></div>
                            <?php endif; ?>
                            
                            <form action="index.php?route=user/changePassword" method="POST">
                                <!-- Current Password -->
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <?php if (!empty($password_errors['current_password'])): ?>
                                        <div class="text-danger"><?= $password_errors['current_password'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- New Password -->
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <?php if (!empty($password_errors['new_password'])): ?>
                                        <div class="text-danger"><?= $password_errors['new_password'] ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                                </div>
                                
                                <!-- Confirm New Password -->
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <?php if (!empty($password_errors['confirm_password'])): ?>
                                        <div class="text-danger"><?= $password_errors['confirm_password'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>