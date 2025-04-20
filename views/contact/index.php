<main class="container-fluid px-4">
    <div class="section-heading mb-4">
        <h3>Liên hệ với chúng tôi</h3>
        <div class="separator"></div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-5">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="contact-card h-100">
                <div class="card-body text-center">
                    <div class="contact-icon mb-3">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="card-title">Địa chỉ</h5>
                    <p class="card-text">267 Lý Thường Kiệt, Phường 15,<br>Quận 11, TP Hồ Chí Minh</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="contact-card h-100">
                <div class="card-body text-center">
                    <div class="contact-icon mb-3">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <h5 class="card-title">Điện thoại</h5>
                    <p class="card-text">
                        <a href="tel:+84123456789" class="contact-link">+84 123 456 789</a><br>
                        <span class="text-muted small">Thứ 2 - Thứ 7 : 8h - 17h</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card h-100">
                <div class="card-body text-center">
                    <div class="contact-icon mb-3">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <h5 class="card-title">Email</h5>
                    <p class="card-text">
                        <a href="mailto:info@curtaincall.com" class="contact-link">info@curtaincall.com</a><br>
                        <a href="mailto:support@curtaincall.com" class="contact-link">support@curtaincall.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="section-heading mb-4">
                <h3>Gửi tin nhắn cho chúng tôi</h3>
                <div class="separator"></div>
            </div>

            <?php
            $errors = $_SESSION['contact_errors'] ?? [];
            $data = $_SESSION['contact_data'] ?? [];
            unset($_SESSION['contact_errors'], $_SESSION['contact_data']);
            ?>

            <form action="index.php?route=contact/send" method="POST" class="contact-form">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                id="name" name="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Chủ đề <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-chat-left-text-fill"></i></span>
                        <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>"
                            id="subject" name="subject" value="<?= htmlspecialchars($data['subject'] ?? '') ?>">
                        <?php if (isset($errors['subject'])): ?>
                            <div class="invalid-feedback"><?= $errors['subject'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-pencil-fill"></i></span>
                        <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>"
                            id="message" name="message" rows="5"><?= htmlspecialchars($data['message'] ?? '') ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <div class="invalid-feedback"><?= $errors['message'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill me-2"></i>Gửi tin nhắn
                    </button>
                </div>
            </form>
        </div>

        <div class="col-lg-5 mt-5 mt-lg-0">
            <div class="section-heading mb-4">
                <h3 class="fw-bold">Vị trí</h3>
                <div class="separator"></div>
            </div>
            <div class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.520777422487!2d106.65518350880792!3d10.771368259246382!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752ec16cd76359%3A0x3f0d03d1cbdc664b!2zMjY3IEzDvSBUaMaw4budbmcgS2nhu4d0LCBQaMaw4budbmcgMTUsIFF14bqtbiAxMSwgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1745086385350!5m2!1svi!2s"
                    width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</main>