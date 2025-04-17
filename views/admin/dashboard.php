<h1 class="h3 mb-4">Dashboard</h1>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Plays</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_plays'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-film fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Bookings</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-journal-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_users'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Revenue (Monthly)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['monthly_revenue']) ?> đ</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent bookings -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Play</th>
                                <th>Date</th>
                                <th>Seats</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_bookings as $booking): ?>
                            <tr>
                                <td><?= $booking['booking_id'] ?></td>
                                <td><?= htmlspecialchars($booking['username']) ?></td>
                                <td><?= htmlspecialchars($booking['play_title']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($booking['schedule_datetime'])) ?></td>
                                <td><?= $booking['seat_count'] ?></td>
                                <td><?= number_format($booking['amount']) ?> đ</td>
                                <td>
                                    <?php if ($booking['status'] == 'completed'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php elseif ($booking['status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Cancelled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-2">
                    <a href="<?= BASE_URL ?>index.php?route=admin/bookings" class="btn btn-sm btn-primary">View All Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>