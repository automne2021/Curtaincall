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
                <h4 class="play-title mx-3"><?= $play['title'] ?></h4>
                <div class="play-meta bg-dark text-white">
                    <div class="row">
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-calendar-event-fill me-2"></i>
                                <span class="meta-value">
                                    <?php if (!empty($play['date'])): ?>
                                        <?= date("H:i", strtotime($play['start_time'])) ?> - <?= date("H:i", strtotime($play['end_time'])) ?>, <?= date("d \\t\h\á\\n\g m, Y", strtotime($play['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="meta-item">
                                <i class="bi bi-geo-alt-fill me-2"></i>
                                <span class="meta-value"><?= $play['theater_name'] ?></span>
                                <p class="meta-location"><?= $play['theater_location'] ?? 'Location not available' ?></p>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="section-divider">
                            <p class="meta-price mb-1"><span class="text-white">Giá chỉ từ</span> <?= number_format($play['min_price'], 0, ',', '.') ?>đ</p>
                        </div>
                        <div class="col-12">
                            <div class="play-actions text-center">
                                <a href="index.php?route=booking/create&play_id=<?= $play['play_id'] ?>" class="btn">
                                    <i class="bi bi-ticket-perforated me-2"></i>Đặt vé ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-dashed-vertical"></div>
    </div>
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
</main>