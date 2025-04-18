<div class="mb-4 fade-in">
    <a href="<?= BASE_URL ?>index.php?route=admin/users" class="btn btn-outline-primary back-button">
        <i class="bi bi-arrow-left"></i> Back to Users
    </a>
</div>

<div class="user-view-container fade-in" style="animation-delay: 0.1s;">
    <div class="user-header">
        <?php if (!empty($user['avatar'])): ?>
            <img src="<?= BASE_URL . $user['avatar'] ?>" class="user-avatar-large" alt="<?= htmlspecialchars($user['username']) ?>'s avatar">
        <?php else: ?>
            <div class="avatar-placeholder-large">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
            </div>
        <?php endif; ?>
        
        <div class="user-header-content">
            <h1 class="user-name-large"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></h1>
            <p class="user-email-large"><?= htmlspecialchars($user['email']) ?></p>
            <span class="user-id-badge">User ID: <?= htmlspecialchars($user['user_id']) ?></span>
        </div>
    </div>
    
    <div class="user-content">
        <div class="user-actions">
            <button type="button" 
                    class="btn btn-danger" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteUserModal" 
                    data-user-id="<?= $user['user_id'] ?>"
                    data-user-name="<?= htmlspecialchars($user['fullname'] ?? $user['username']) ?>">
                <i class="bi bi-trash"></i> Delete User
            </button>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="user-section">
                    <h2 class="section-title">Personal Information</h2>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-person"></i> Username:</span>
                            <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-envelope"></i> Email:</span>
                            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-card-text"></i> Full Name:</span>
                            <span class="info-value"><?= htmlspecialchars($user['fullname'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-telephone"></i> Phone:</span>
                            <span class="info-value"><?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-calendar-check"></i> Joined:</span>
                            <span class="info-value"><?= date('F j, Y, g:i A', strtotime($user['created_at'])) ?></span>
                        </div>
                        <?php if (!empty($user['google_id'])): ?>
                        <div class="info-item">
                            <span class="info-label"><i class="bi bi-google"></i> Google:</span>
                            <span class="info-value">
                                <span class="badge bg-primary">Connected</span>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="user-section">
                    <h2 class="section-title">Booking Statistics</h2>
                    <div class="user-bookings">
                        <div class="booking-count">
                            <?= count($userBookings) ?> Bookings
                        </div>
                        
                        <?php if (empty($userBookings)): ?>
                            <p class="text-center text-muted">This user has no bookings yet.</p>
                        <?php else: ?>
                            <h6 class="mb-3">Recent Bookings:</h6>
                            <?php foreach (array_slice($userBookings, 0, 5) as $booking): ?>
                                <div class="booking-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="booking-title"><?= htmlspecialchars($booking['play_title']) ?></div>
                                        <span class="booking-status status-badge status-<?= strtolower($booking['status']) ?>">
                                            <?= htmlspecialchars($booking['status']) ?>
                                        </span>
                                    </div>
                                    <div class="booking-details">
                                        <div><i class="bi bi-calendar-event"></i> <?= date('M j, Y', strtotime($booking['schedule_date'] ?? $booking['created_at'])) ?></div>
                                        <div><i class="bi bi-ticket-perforated"></i> Seat: <?= htmlspecialchars($booking['seat_id']) ?></div>
                                    </div>
                                    <div class="text-end mt-2">
                                        <a href="<?= BASE_URL ?>index.php?route=admin/viewBooking&id=<?= $booking['booking_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($userBookings) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="<?= BASE_URL ?>index.php?route=admin/userBookings&id=<?= $user['user_id'] ?>" class="btn btn-outline-primary">
                                        View All Bookings
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger display-5 mb-3"></i>
                    <p>Are you sure you want to delete this user:</p>
                    <h5 class="fw-bold" id="userName"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></h5>
                </div>
                <p class="text-danger mt-3 mb-0 text-center">
                    <small><i class="bi bi-exclamation-triangle"></i> This action cannot be undone. All user data including bookings will be deleted.</small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= BASE_URL ?>index.php?route=admin/deleteUser&id=<?= $user['user_id'] ?>" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>