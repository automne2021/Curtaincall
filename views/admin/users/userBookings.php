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
                    <img src="<?= BASE_URL . $user['avatar'] ?>" class="user-avatar-small me-3" alt="<?= htmlspecialchars($user['username']) ?>'s avatar">
                <?php else: ?>
                    <div class="avatar-placeholder-small me-3">
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
                        <?php 
                        // Pagination setup
                        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                        $per_page = 5;
                        $total_bookings = count($bookings);
                        $total_pages = ceil($total_bookings / $per_page);
                        
                        // Ensure current page is valid
                        $page = min($page, max(1, $total_pages));
                        
                        // Get bookings for current page
                        $start_index = ($page - 1) * $per_page;
                        $current_page_bookings = array_slice($bookings, $start_index, $per_page);
                        
                        foreach ($current_page_bookings as $index => $booking): 
                        ?>
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
                
                <?php if ($total_pages > 1): ?>
                    <?php 
                    // Base URL for pagination
                    $base_url = BASE_URL . 'index.php?route=admin/userBookings&id=' . $user['user_id'];
                    
                    // Pagination info
                    $pagination = [
                        'total' => $total_bookings,
                        'per_page' => $per_page,
                        'current_page' => $page,
                        'last_page' => $total_pages
                    ];
                    
                    // Include pagination component
                    include 'views/admin/pagination.php';
                    ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Add these styles for smaller avatars */
.user-avatar-small {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #f8f9fa;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.avatar-placeholder-small {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #adb5bd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
}
</style>