<div class="container-fluid px-4">
    <h1 class="h3 mb-4">Dashboard</h1>
    
    <!-- Stats cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_users'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Plays</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_plays'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-camera-reels fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Theaters</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_theaters'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking status chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Booking Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie mb-4">
                        <canvas id="bookingStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-success"></i> Paid (<?= $paid_count ?>)
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-warning"></i> Pending (<?= $pending_count ?>)
                        </span>
                        <span class="mr-2">
                            <i class="bi bi-circle-fill text-danger"></i> Cancelled/Expired (<?= $cancelled_count + $expired_count ?>)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Revenue -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Revenue (Last 7 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Recent Bookings</h6>
                    <a href="index.php?route=admin/bookings" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Play</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td>#<?= $booking['booking_id'] ?></td>
                                    <td><?= htmlspecialchars($booking['username']) ?></td>
                                    <td><?= htmlspecialchars($booking['play_title']) ?></td>
                                    <td><?= date('M j, Y', strtotime($booking['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $booking['status'] == 'Paid' ? 'success' : ($booking['status'] == 'Pending' ? 'warning' : 'danger') ?>">
                                            <?= $booking['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($recent_bookings)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No bookings found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Popular Plays -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Popular Plays</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($popular_plays as $play): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($play['title']) ?>
                            <span class="badge bg-primary rounded-pill"><?= $play['bookings_count'] ?> bookings</span>
                        </li>
                        <?php endforeach; ?>
                        
                        <?php if (empty($popular_plays)): ?>
                        <li class="list-group-item text-center">No data available</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Booking Status Chart
const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Paid', 'Pending', 'Cancelled/Expired'],
        datasets: [{
            data: [<?= $paid_count ?>, <?= $pending_count ?>, <?= $cancelled_count + $expired_count ?>],
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#17a673', '#dda20a', '#be3e32'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%',
    },
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: [
            <?php 
            $dates = [];
            foreach (array_reverse($recent_revenue) as $rev) {
                $dates[] = "'" . date('M j', strtotime($rev['date'])) . "'";
            }
            echo implode(', ', $dates);
            ?>
        ],
        datasets: [{
            label: "Revenue",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [
                <?php
                $revenues = [];
                foreach (array_reverse($recent_revenue) as $rev) {
                    $revenues[] = $rev['revenue'];
                }
                echo implode(', ', $revenues);
                ?>
            ],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            },
            y: {
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    // Include a dollar sign in the ticks
                    callback: function(value, index, values) {
                        return value.toLocaleString() + ' VND';
                    }
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            },
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>