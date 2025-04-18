<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">Create New Theater</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/theaters" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Back to Theaters
    </a>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <form action="<?= BASE_URL ?>index.php?route=admin/createTheater" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="theater_id" class="form-label">Theater ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['theater_id']) ? 'is-invalid' : '' ?>" 
                        id="theater_id" name="theater_id" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['theater_id'] ?? '') ?>" required
                        placeholder="3 uppercase letters (e.g., IDE)">
                    <?php if (isset($_SESSION['form_errors']['theater_id'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['theater_id'] ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Enter a unique 3-letter code for this theater.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Theater Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['name']) ? 'is-invalid' : '' ?>" 
                        id="name" name="name" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['form_errors']['name'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['name'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['location']) ? 'is-invalid' : '' ?>" 
                        id="location" name="location" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['location'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['form_errors']['location'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['location'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-end mt-4">
                <a href="<?= BASE_URL ?>index.php?route=admin/theaters" class="btn btn-outline-secondary me-2">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Theater
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Clear form data and errors after displaying them
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>