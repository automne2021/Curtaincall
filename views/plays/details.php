<main class="container-fluid px-3">
    <div class="row my-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="index.php?route=play&theater_id=<?= $play['theater_id'] ?>"><?= $play['theater_name'] ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $play['title'] ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="play-details-container bg-dark">
        <div class="row h-100">
            <!-- Play image -->
            <div class="col-md-8 play-image-column">
                <img src="<?= $play['image'] ?>" class="play-detail-img" alt="<?= $play['title'] ?>">
            </div>

            <!-- Play details -->
            <div class="col-md-4">
                <h4 class="play-title mx-3"><?= '[', $play['theater_name'], '] ', $play['title'] ?></h4>
                <div class="play-meta bg-dark text-white">
                    <div class="row">
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event me-2"></i>
                                <div class="meta-value">
                                    <?php if (!empty($play['date'])): ?>
                                        <?= date("H:i", strtotime($play['start_time'])) ?> - <?= date("H:i", strtotime($play['end_time'])) ?>, <?= date("d \\t\h\á\\n\g m, Y", strtotime($play['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-geo-alt me-2"></i>
                                <div class="meta-value"><?= $play['theater_name'] ?></div>
                                <p class="meta-location"><?= $play['theater_location'] ?? 'Location not available' ?></p>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="section-divider">
                            <p class="meta-price mb-1"><span class="text-white">Giá chỉ từ</span> <?= number_format($play['min_price'], 0, ',', '.') ?>đ</p>
                        </div>
                    </div>
                </div>
                <div class="play-actions text-center">
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="#scheduleSection" class="btn">
                            <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                        </a>
                        <?php $_SESSION['redirect_after_login'] = 'index.php?route=booking/create&play_id=' . $play['play_id'] . '#scheduleSection'; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <div class="border-dashed-vertical"></div>
    </div>
    <?php if (!isset($_SESSION['user'])): ?>
        <?php include 'views/auth/login-modal.php'; ?>
    <?php endif; ?>
    <div class="play-description my-4">
        <h5 class="section-title">Giới thiệu</h5>
        <hr class="section-divider">
        <p class="description-content"><?= $play['description'] ?></p>
    </div>

    <?php if (isset($seats_result) && $seats_result->num_rows > 0): ?>
        <div class="play-tickets mb-4">
            <h5 class="section-title">Ticket Information</h5>

            <div class="ticket-categories">
                <div class="row">
                    <?php while ($seat = $seats_result->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="ticket-category-card <?= $seat['seat_type'] == 'VIP' ? 'vip' : '' ?>">
                                <div class="card-header">
                                    <?= $seat['seat_type'] ?> Seats
                                </div>
                                <div class="card-body">
                                    <div class="price-tag"><?= number_format($prices[$seat['seat_type']] ?? 0, 0, ',', '.') ?>đ</div>
                                    <div class="availability">
                                        <span class="availability-text">Availability:</span>
                                        <span class="seats-remaining"><?= $seat['available_seats'] ?>/<?= $seat['total_seats'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Play schedule -->
    <div class="play-schedule my-4" id="scheduleSection">
        <h5 class="section-title">Lịch diễn</h5>
        <hr class="section-divider">

        <div class="card">
            <div class="card-body">
                <?php
                $schedules_sql = "SELECT * FROM schedules WHERE play_id = ? AND date >= CURDATE() ORDER BY date, start_time";
                $stmt = $conn->prepare($schedules_sql);
                $stmt->bind_param("i", $play['play_id']);
                $stmt->execute();
                $schedules_result = $stmt->get_result();

                if ($schedules_result->num_rows > 0) {
                    $dates = [];
                    $schedules = [];
                    while ($schedule = $schedules_result->fetch_assoc()) {
                        $date = $schedule['date'];
                        if (!in_array($date, $dates)) {
                            $dates[] = $date;
                        }
                        $schedules[] = $schedule;
                    }
                ?>
                    <form action="index.php?route=booking/selectSeats" method="POST" id="scheduleForm">
                        <input type="hidden" name="play_id" value="<?= $play['play_id'] ?>">

                        <div class="form-group mb-4">
                            <label for="schedule_date" class="form-label">Chọn ngày:</label>
                            <select name="schedule_date" id="schedule_date" class="form-select mb-3" required>
                                <option value="">-- Chọn ngày --</option>
                                <?php foreach ($dates as $date): ?>
                                    <?php $formatted_date = date('l, d/m/Y', strtotime($date)); ?>
                                    <option value="<?= $date ?>"><?= $formatted_date ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div id="time-slots" class="d-none">
                                <?php foreach ($dates as $date): ?>
                                    <div class="time-slot" data-date="<?= $date ?>">
                                        <label class="form-label">Chọn giờ:</label>
                                        <div class="time-buttons">
                                            <?php foreach ($schedules as $schedule): ?>
                                                <?php if ($schedule['date'] === $date): ?>
                                                    <div class="form-check time-option">
                                                        <input type="radio" class="form-check-input" name="schedule_time"
                                                            id="time_<?= $schedule['schedule_id'] ?>"
                                                            value="<?= $schedule['schedule_id'] ?>" required>
                                                        <label class="form-check-label" for="time_<?= $schedule['schedule_id'] ?>">
                                                            <?= date('H:i', strtotime($schedule['start_time'])) ?> -
                                                            <?= date('H:i', strtotime($schedule['end_time'])) ?>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn auth-btn">
                                <i class="bi bi-arrow-right"></i> Tiếp tục chọn ghế
                            </button>
                        </div>
                    </form>

                <?php } else { ?>
                    <div class="alert alert-info">
                        Hiện không có lịch diễn nào cho vở kịch này.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>