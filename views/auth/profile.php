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
                    $avatarUrl = '';
                    // Check if avatar is an external URL or local path
                    if (isset($user['avatar']) && $user['avatar']) {
                        // from Google
                        if (strpos($user['avatar'], 'http') === 0) {
                            $avatarUrl = $user['avatar'];
                        } else {
                            $avatarUrl = BASE_URL . $user['avatar'];
                        }
                    } else {
                        // Default avatar
                        $avatarUrl = BASE_URL . 'public/images/avatars/default.png';
                    }
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

                                <!-- Add Google Account Connection Section -->
                                <div class="mb-4">
                                    <label class="form-label">Liên kết tài khoản</label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="mb-1">Tài khoản Google</h6>
                                                    <p class="text-muted mb-0 small">
                                                        <?php if (isset($user['google_id']) && $user['google_id']): ?>
                                                            <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Đã kết nối</span>
                                                        <?php else: ?>
                                                            <span class="text-muted">Chưa kết nối</span>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                                <?php if (isset($user['google_id']) && $user['google_id']): ?>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                        <i class="bi bi-google me-1"></i>Đã kết nối
                                                    </button>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>index.php?route=user/google-login" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-google me-1"></i>Kết nối
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
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

                <!-- Connected Accounts Tab -->
                <div class="tab-pane fade" id="connected-accounts">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Tài khoản liên kết</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i> Kết nối tài khoản mạng xã hội để đăng nhập nhanh hơn.
                            </div>

                            <!-- Google Account -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="social-icon google-icon me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 48 48">
                                                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                                                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                                                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Google</h6>
                                                <p class="mb-0 text-muted small">
                                                    <?php if (isset($user['google_id']) && $user['google_id']): ?>
                                                        <span class="text-success">Đã kết nối với tài khoản Google của bạn</span>
                                                    <?php else: ?>
                                                        <span>Kết nối để đăng nhập nhanh hơn</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if (isset($user['google_id']) && $user['google_id']): ?>
                                                <form action="index.php?route=user/disconnectGoogle" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy kết nối tài khoản Google?');">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Hủy kết nối</button>
                                                </form>
                                            <?php else: ?>
                                                <a href="<?= BASE_URL ?>index.php?route=user/google-login" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-google me-1"></i>Kết nối
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning mt-4">
                                <i class="bi bi-exclamation-triangle me-2"></i> Nếu bạn đăng nhập bằng tài khoản xã hội và chưa đặt mật khẩu, bạn có thể tạo mật khẩu bằng cách sử dụng tính năng "Quên mật khẩu".
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="public/js/profile-validation.js"></script>
</main>