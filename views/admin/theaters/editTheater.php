<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">Edit Theater</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/theaters" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Back to Theaters
    </a>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <form action="<?= BASE_URL ?>index.php?route=admin/editTheater&id=<?= $theater['theater_id'] ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="theater_id" class="form-label">Theater ID</label>
                    <input type="text" class="form-control" 
                        id="theater_id" value="<?= htmlspecialchars($theater['theater_id']) ?>" readonly disabled>
                    <small class="form-text text-muted">Theater ID cannot be changed.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Theater Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['name']) ? 'is-invalid' : '' ?>" 
                        id="name" name="name" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['name'] ?? $theater['name']) ?>" required>
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
                        value="<?= htmlspecialchars($_SESSION['form_data']['location'] ?? $theater['location']) ?>" required>
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
                    <i class="bi bi-save"></i> Update Theater
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