<div class="dashboard-wrapper">
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="dashboard-stats-title text-primary">Total Plays</h6>
                            <h2 class="dashboard-stats-value"><?= $stats['total_plays'] ?></h2>
                        </div>
                        <i class="bi bi-film dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="dashboard-stats-title text-success">Total Bookings</h6>
                            <h2 class="dashboard-stats-value"><?= $stats['total_bookings'] ?></h2>
                        </div>
                        <i class="bi bi-journal-check dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="dashboard-stats-title text-info">Total Users</h6>
                            <h2 class="dashboard-stats-value"><?= $stats['total_users'] ?></h2>
                        </div>
                        <i class="bi bi-people dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="dashboard-card border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="dashboard-stats-title text-warning">Monthly Revenue</h6>
                            <h2 class="dashboard-stats-value"><?= number_format($stats['monthly_revenue']) ?> đ</h2>
                        </div>
                        <i class="bi bi-currency-dollar dashboard-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="chart-container">
                <h5 class="chart-title">Revenue Overview</h5>
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bookings by Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="chart-container">
                <h5 class="chart-title">Bookings by Status</h5>
                <div class="chart-pie">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Plays -->
        <div class="col-lg-12">
            <div class="data-table">
                <div class="data-table-header d-flex justify-content-between align-items-center">
                    <h5 class="data-table-title">Popular Plays</h5>
                </div>
                <div class="data-table-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Play</th>
                                    <th class="text-end">Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($popular_plays as $play): ?>
                                <tr>
                                    <td><?= htmlspecialchars($play['title']) ?></td>
                                    <td class="text-end fw-semibold"><?= $play['booking_count'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent bookings -->
    <div class="row">
        <div class="col-lg-12">
            <div class="data-table">
                <div class="data-table-header d-flex justify-content-between align-items-center">
                    <h5 class="data-table-title">Recent Bookings</h5>
                    <a href="<?= BASE_URL ?>index.php?route=admin/bookings" class="view-all-btn">View All <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="data-table-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Play</th>
                                    <th>Seat</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_bookings)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No recent bookings found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                    <tr>
                                        <td><?= $booking['booking_id'] ?></td>
                                        <td><?= htmlspecialchars($booking['username']) ?></td>
                                        <td><?= htmlspecialchars($booking['play_title']) ?></td>
                                        <td><?= htmlspecialchars($booking['seat_id']) ?></td>
                                        <td><?= number_format($booking['amount']) ?> đ</td>
                                        <td>
                                            <?php if ($booking['status'] == 'Paid'): ?>
                                                <span class="status-badge status-paid">Paid</span>
                                            <?php elseif ($booking['status'] == 'Pending'): ?>
                                                <span class="status-badge status-pending">Pending</span>
                                            <?php elseif ($booking['status'] == 'Expired'): ?>
                                                <span class="status-badge status-expired">Expired</span>
                                            <?php else: ?>
                                                <span class="status-badge"><?= $booking['status'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Data for charts from PHP
const revenueChartData = <?= json_encode($revenue_chart_data) ?>;
const bookingsChartData = <?= json_encode($bookings_chart_data) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    if (document.getElementById('revenueChart')) {
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: revenueChartData.labels,
                datasets: [{
                    label: 'Revenue (đ)',
                    data: revenueChartData.data,
                    backgroundColor: 'rgba(7, 94, 84, 0.05)',
                    borderColor: '#075E54',
                    borderWidth: 2,
                    pointBackgroundColor: '#075E54',
                    pointRadius: 4,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString() + ' đ';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Bookings by Status Chart
    if (document.getElementById('bookingsChart')) {
        const statusCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: bookingsChartData.labels,
                datasets: [{
                    data: bookingsChartData.data,
                    backgroundColor: [
                        '#075E54', // Primary
                        '#198754', // Success
                        '#0dcaf0', // Info
                        '#ffc107', // Warning
                        '#dc3545'  // Danger
                    ],
                    hoverOffset: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
    }
});
</script>