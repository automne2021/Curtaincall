<?php
require_once 'helpers/sort_helpers.php';
?>

<main class="container-fluid px-4 mt-4">
    <!-- Hot Performances Carousel -->
    <div class="row mb-5">
        <div class="col-12">
            <div id="hotPerformancesCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Indicators -->
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>

                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    <?php
                    $slide = 0;
                    $total_slides = ceil($hot_plays->num_rows / 2);

                    for ($i = 0; $i < $total_slides; $i++):
                        $hot_plays->data_seek($i * 2);
                    ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <div class="row">
                                <?php for ($j = 0; $j < 2 && $play = $hot_plays->fetch_assoc(); $j++): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="banner-card">
                                            <a href="index.php?route=play/view&play_id=<?= $play['play_id'] ?>">
                                                <img src="<?= $play['image'] ?>" class="d-block w-100 rounded" alt="<?= $play['title'] ?>">
                                                <div class="banner-overlay">
                                                    <button class="btn btn-sm">Xem chi tiết</button>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <!-- Controls -->
                <button class="carousel-control-prev carousel-custom-control" type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next carousel-custom-control" type="button" data-bs-target="#hotPerformancesCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon text-primary" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

    <!-- UpComing Performances Section -->
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-left align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="orange" class="bi bi-fire" viewBox="0 0 16 16">
                <path d="M8 16c3.314 0 6-2 6-5.5 0-1.5-.5-4-2.5-6 .25 1.5-1.25 2-1.25 2C11 4 9 .5 6 0c.357 2 .5 4-2 6-1.25 1-2 2.729-2 4.5C2 14 4.686 16 8 16m0-1c-1.657 0-3-1-3-2.75 0-.75.25-2 1.25-3C6.125 10 7 10.5 7 10.5c-.375-1.25.5-3.25 2-3.5-.179 1-.25 2 1 3 .625.5 1 1.364 1 2.25C11 14 9.657 15 8 15" />
            </svg>
            <h5 class="mx-2 mb-0">Sự kiện sắp tới</h5>
        </div>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <a href="index.php?route=play/view&play_id=<?= $row['play_id'] ?>" class="play-card-link">
                        <div class="play-card">
                            <img src="<?= $row['image'] ?>" class="card-img-top" loading="lazy" alt="<?= $row['title'] ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= '[', $row['theater_name'], '] ', $row['title'] ?></h5>
                                <p class="fw-bold mb-1">Từ <?= number_format($row['min_price'], 0, ',', '.') ?>đ</p>
                                <p class="date-info"><i class="bi bi-calendar-event me-2"></i>
                                    <?php if (!empty($row['date'])): ?>
                                        <?= date("d \\t\h\á\\n\g m, Y", strtotime($row['date'])) ?>
                                    <?php else: ?>
                                        Sự kiện đã kết thúc
                                    <?php endif; ?>

                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center">Không có vở diễn nào được tìm thấy.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- "Load More" button section -->
    <div class="row" id="load-more-row">
        <div class="col-12 text-center my-3" id="load-more-container">
            <div class="spinner-border text-primary d-none" id="loading-spinner" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
        </div>
    </div>
</main>

<script>
    const playConfig = {
        currentPage: <?= $page ?? 1 ?>,
        totalPages: <?= $total_pages ?? 1 ?>,
        theaterId: '<?= $theater_id ?? '' ?>',
        sortField: '<?= $sort_field ?? 'date' ?>',
        sortDir: '<?= $sort_dir ?? 'desc' ?>'
    };
</script>
<script src="public/js/lazy-loading.js"></script>