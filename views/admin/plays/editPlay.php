<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Edit Play</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/plays" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Plays
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= BASE_URL ?>index.php?route=admin/editPlay&id=<?= $play['play_id'] ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['title']) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? $play['title']) ?>" required>
                    <?php if (isset($_SESSION['form_errors']['title'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['title'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="theater_id" class="form-label">Theater <span class="text-danger">*</span></label>
                    <select class="form-control <?= isset($_SESSION['form_errors']['theater_id']) ? 'is-invalid' : '' ?>" 
                            id="theater_id" name="theater_id" required>
                        <?php foreach ($theaters as $theater): ?>
                            <option value="<?= $theater['theater_id'] ?>" 
                                <?= (isset($_SESSION['form_data']['theater_id']) ? $_SESSION['form_data']['theater_id'] : $play['theater_id']) == $theater['theater_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($theater['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($_SESSION['form_errors']['theater_id'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['theater_id'] ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="duration" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?= isset($_SESSION['form_errors']['duration']) ? 'is-invalid' : '' ?>" 
                           id="duration" name="duration" min="1" 
                           value="<?= isset($_SESSION['form_data']['duration']) ? htmlspecialchars($_SESSION['form_data']['duration']) : $play['duration'] ?>" required>
                    <?php if (isset($_SESSION['form_errors']['duration'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['duration'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Play Image</label>
                    <?php if (!empty($play['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL . $play['image'] ?>" alt="Current Image" id="imagePreview" style="max-height: 150px; max-width: 100%;">
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control <?= isset($_SESSION['form_errors']['image']) ? 'is-invalid' : '' ?>" 
                           id="image" name="image" accept="image/*">
                    <div class="form-text">Leave empty to keep the current image. Recommended size: 800x600 pixels</div>
                    <?php if (isset($_SESSION['form_errors']['image'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['image'] ?></div>
                    <?php endif; ?>
                    <div id="imagePreviewContainer"></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="director" class="form-label">Director</label>
                    <input type="text" class="form-control" id="director" name="director" 
                           value="<?= isset($_SESSION['form_data']['director']) ? htmlspecialchars($_SESSION['form_data']['director']) : htmlspecialchars($play['director'] ?? '') ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="cast" class="form-label">Cast</label>
                    <input type="text" class="form-control" id="cast" name="cast" 
                           value="<?= isset($_SESSION['form_data']['cast']) ? htmlspecialchars($_SESSION['form_data']['cast']) : htmlspecialchars($play['cast'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control <?= isset($_SESSION['form_errors']['description']) ? 'is-invalid' : '' ?>" 
                          id="description" name="description" rows="5" required><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : htmlspecialchars($play['description']) ?></textarea>
                <?php if (isset($_SESSION['form_errors']['description'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['form_errors']['description'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Play
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