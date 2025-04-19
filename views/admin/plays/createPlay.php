<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Add New Play</h1>
    <a href="<?= BASE_URL ?>index.php?route=admin/plays" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Plays
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="<?= BASE_URL ?>index.php?route=admin/createPlay" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['title']) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['title'] ?? '') ?>" required>
                    <?php if (isset($_SESSION['form_errors']['title'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['title'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="play_id" class="form-label">Play ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($_SESSION['form_errors']['play_id']) ? 'is-invalid' : '' ?>" 
                           id="play_id" name="play_id" placeholder="e.g. IDE09" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['play_id'] ?? '') ?>" required>
                    <small class="form-text text-muted">Format should match existing IDs (e.g., IDE09, THN05)</small>
                    <?php if (isset($_SESSION['form_errors']['play_id'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['play_id'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="theater_id" class="form-label">Theater <span class="text-danger">*</span></label>
                    <select class="form-control <?= isset($_SESSION['form_errors']['theater_id']) ? 'is-invalid' : '' ?>" 
                            id="theater_id" name="theater_id" required>
                        <option value="" disabled selected>Select Theater</option>
                        <?php foreach ($theaters as $theater): ?>
                            <option value="<?= $theater['theater_id'] ?>" 
                                <?= (isset($_SESSION['form_data']['theater_id']) && $_SESSION['form_data']['theater_id'] == $theater['theater_id']) ? 'selected' : '' ?>>
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
                    <input type="file" class="form-control <?= isset($_SESSION['form_errors']['image']) ? 'is-invalid' : '' ?>" 
                           id="image" name="image" accept="image/*">
                    <div class="form-text">Recommended size: 800x600 pixels</div>
                    <?php if (isset($_SESSION['form_errors']['image'])): ?>
                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['image'] ?></div>
                    <?php endif; ?>
                    <div id="imagePreviewContainer"></div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Schedule Information</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addScheduleBtn">
                        <i class="bi bi-plus-circle"></i> Add Schedule
                    </button>
                </div>
                <div class="card-body">
                    <div id="schedules-container">
                        <!-- Initial schedule form -->
                        <div class="schedule-item mb-3 border-bottom pb-3">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="date_0" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?= isset($_SESSION['form_errors']['date_0']) ? 'is-invalid' : '' ?>" 
                                        id="date_0" name="schedules[0][date]" 
                                        value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][0]['date'] ?? '') ?>" required>
                                    <?php if (isset($_SESSION['form_errors']['date_0'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['date_0'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="start_time_0" class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control <?= isset($_SESSION['form_errors']['start_time_0']) ? 'is-invalid' : '' ?>" 
                                        id="start_time_0" name="schedules[0][start_time]" 
                                        value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][0]['start_time'] ?? '') ?>" required>
                                    <?php if (isset($_SESSION['form_errors']['start_time_0'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['start_time_0'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="end_time_0" class="form-label">End Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control <?= isset($_SESSION['form_errors']['end_time_0']) ? 'is-invalid' : '' ?>" 
                                        id="end_time_0" name="schedules[0][end_time]" 
                                        value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][0]['end_time'] ?? '') ?>" required>
                                    <?php if (isset($_SESSION['form_errors']['end_time_0'])): ?>
                                        <div class="invalid-feedback"><?= $_SESSION['form_errors']['end_time_0'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional schedules will be added here by JavaScript -->
                        <?php 
                        // Display existing schedules if any from the form data
                        if (isset($_SESSION['form_data']['schedules']) && count($_SESSION['form_data']['schedules']) > 1): 
                            for ($i = 1; $i < count($_SESSION['form_data']['schedules']); $i++):
                        ?>
                            <div class="schedule-item mb-3 border-bottom pb-3">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="date_<?= $i ?>" class="form-label">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control <?= isset($_SESSION['form_errors']['date_' . $i]) ? 'is-invalid' : '' ?>" 
                                            id="date_<?= $i ?>" name="schedules[<?= $i ?>][date]" 
                                            value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][$i]['date'] ?? '') ?>" required>
                                        <?php if (isset($_SESSION['form_errors']['date_' . $i])): ?>
                                            <div class="invalid-feedback"><?= $_SESSION['form_errors']['date_' . $i] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="start_time_<?= $i ?>" class="form-label">Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control <?= isset($_SESSION['form_errors']['start_time_' . $i]) ? 'is-invalid' : '' ?>" 
                                            id="start_time_<?= $i ?>" name="schedules[<?= $i ?>][start_time]" 
                                            value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][$i]['start_time'] ?? '') ?>" required>
                                        <?php if (isset($_SESSION['form_errors']['start_time_' . $i])): ?>
                                            <div class="invalid-feedback"><?= $_SESSION['form_errors']['start_time_' . $i] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label for="end_time_<?= $i ?>" class="form-label">End Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control <?= isset($_SESSION['form_errors']['end_time_' . $i]) ? 'is-invalid' : '' ?>" 
                                            id="end_time_<?= $i ?>" name="schedules[<?= $i ?>][end_time]" 
                                            value="<?= htmlspecialchars($_SESSION['form_data']['schedules'][$i]['end_time'] ?? '') ?>" required>
                                        <?php if (isset($_SESSION['form_errors']['end_time_' . $i])): ?>
                                            <div class="invalid-feedback"><?= $_SESSION['form_errors']['end_time_' . $i] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-schedule">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        <?php 
                            endfor; 
                        endif; 
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control ckeditor <?= isset($_SESSION['form_errors']['description']) ? 'is-invalid' : '' ?>" 
                        id="description" name="description" rows="10" 
                        data-placeholder="Enter play description here..."
                        required><?= $_SESSION['form_data']['description'] ?? '' ?></textarea>
                <?php if (isset($_SESSION['form_errors']['description'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['form_errors']['description'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save Play
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= BASE_URL ?>public/js/schedule-addremove.js"></script>

<?php
// Clear form data and errors after displaying them
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);
?>