<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <h1 class="h3">Bookings Management</h1>
</div>

<div class="card shadow mb-4 fade-in" style="animation-delay: 0.1s;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern" id="bookingsTable">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Play</th>
                        <th>Theater</th>
                        <th>Seat</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center empty-table">
                                <i class="bi bi-exclamation-circle"></i>
                                <p>No bookings found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $index => $booking): ?>
                            <tr style="animation-delay: <?= 0.1 + ($index * 0.05) ?>s;" class="fade-in">
                                <td class="booking-id"><?= htmlspecialchars($booking['booking_id']) ?></td>
                                <td><?= htmlspecialchars($booking['username']) ?></td>
                                <td><?= htmlspecialchars($booking['play_title']) ?></td>
                                <td><?= htmlspecialchars($booking['theater_name']) ?></td>
                                <td><?= htmlspecialchars($booking['seat_id']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($booking['status']) ?>">
                                        <?= htmlspecialchars($booking['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center action-buttons">
                                    <a href="<?= BASE_URL ?>index.php?route=admin/viewBooking&id=<?= $booking['booking_id'] ?>" 
                                        class="btn btn-icon btn-view" data-bs-toggle="tooltip" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include pagination -->
<?php if (!empty($bookings) && $pagination['last_page'] > 1): ?>
    <?php include 'views/admin/pagination.php'; ?>
<?php endif; ?>