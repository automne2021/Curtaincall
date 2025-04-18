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
                    <label for="play_id" class="form-label">Play ID</label>
                    <input type="text" class="form-control" id="play_id" value="<?= htmlspecialchars($play['play_id']) ?>" readonly>
                    <small class="form-text text-muted">Play ID cannot be changed</small>
                </div>
            </div>

            <div class="row">
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
                
                <div class="col-md-6 mb-3">
                    <label for="image" class="form-label">Play Image</label>
                    <?php if (!empty($play['image'])): ?>
                    <div class="mb-2">
                        <img src="<?= BASE_URL . $play['image'] ?>" alt="Current Image" id="currentImagePreview" style="max-height: 150px; max-width: 100%;">
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

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Schedule Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?= isset($_SESSION['form_errors']['date']) ? 'is-invalid' : '' ?>" 
                                id="date" name="date" 
                                value="<?= htmlspecialchars($_SESSION['form_data']['date'] ?? $schedule['date'] ?? '') ?>" required>
                            <?php if (isset($_SESSION['form_errors']['date'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['form_errors']['date'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= isset($_SESSION['form_errors']['start_time']) ? 'is-invalid' : '' ?>" 
                                id="start_time" name="start_time" 
                                value="<?= htmlspecialchars($_SESSION['form_data']['start_time'] ?? $schedule['start_time'] ?? '') ?>" required>
                            <?php if (isset($_SESSION['form_errors']['start_time'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['form_errors']['start_time'] ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control <?= isset($_SESSION['form_errors']['end_time']) ? 'is-invalid' : '' ?>" 
                                id="end_time" name="end_time" 
                                value="<?= htmlspecialchars($_SESSION['form_data']['end_time'] ?? $schedule['end_time'] ?? '') ?>" required>
                            <?php if (isset($_SESSION['form_errors']['end_time'])): ?>
                                <div class="invalid-feedback"><?= $_SESSION['form_errors']['end_time'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control ckeditor <?= isset($_SESSION['form_errors']['description']) ? 'is-invalid' : '' ?>" 
                        id="description" name="description" rows="10" 
                        data-placeholder="Enter play description here..."
                        required><?= isset($_SESSION['form_data']['description']) ? $_SESSION['form_data']['description'] : $play['description'] ?></textarea>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate duration automatically when start or end times change
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    function updateDuration() {
        if (startTimeInput.value && endTimeInput.value) {
            const startParts = startTimeInput.value.split(':');
            const endParts = endTimeInput.value.split(':');
            
            const startMinutes = (parseInt(startParts[0]) * 60) + parseInt(startParts[1]);
            const endMinutes = (parseInt(endParts[0]) * 60) + parseInt(endParts[1]);
            
            // Handle cases where the show goes past midnight
            const durationMinutes = endMinutes < startMinutes ? 
                (24 * 60 - startMinutes) + endMinutes : 
                endMinutes - startMinutes;
            
            console.log(`Duration: ${durationMinutes} minutes`);
        }
    }
    
    startTimeInput.addEventListener('change', updateDuration);
    endTimeInput.addEventListener('change', updateDuration);
});
</script>

<?php
// Clear form data and errors after displaying them
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>