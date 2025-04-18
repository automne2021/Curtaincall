<div class="mb-4 fade-in">
    <a href="<?= BASE_URL ?>index.php?route=admin/viewUser&id=<?= $user['user_id'] ?>" class="btn btn-outline-primary back-button">
        <i class="bi bi-arrow-left"></i> Back to User Profile
    </a>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= BASE_URL . $user['avatar'] ?>" class="user-avatar me-3" alt="<?= htmlspecialchars($user['username']) ?>'s avatar">
                <?php else: ?>
                    <div class="avatar-placeholder me-3">
                        <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="h4 mb-0"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?>'s Bookings</h1>
                    <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
            <span class="badge bg-primary"><?= count($bookings) ?> Bookings</span>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($bookings)): ?>
            <div class="text-center empty-table py-5">
                <i class="bi bi-ticket-perforated"></i>
                <p>This user has no bookings</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Play</th>
                            <th>Date</th>
                            <th>Theater</th>
                            <th>Seat</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $index => $booking): ?>
                        <tr style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;" class="fade-in">
                            <td class="booking-id"><?= htmlspecialchars($booking['booking_id']) ?></td>
                            <td><?= htmlspecialchars($booking['play_title']) ?></td>
                            <td><?= date('M j, Y', strtotime($booking['schedule_date'] ?? $booking['created_at'])) ?></td>
                            <td><?= htmlspecialchars($booking['theater_name']) ?></td>
                            <td><?= htmlspecialchars($booking['seat_id']) ?></td>
                            <td><?= number_format($booking['amount']) ?> VND</td>
                            <td>
                                <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?route=admin/viewBooking&id=<?= $booking['booking_id'] ?>" 
                                   class="btn btn-icon btn-view" data-bs-toggle="tooltip" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>