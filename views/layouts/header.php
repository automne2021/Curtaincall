<?php
// filepath: c:\Users\VY\Downloads\curtaincall\views\layouts\header.php
// Get current route for active nav highlighting
$current_route = $_GET['route'] ?? 'home';

// Determine if this is a booking page
$isBookingPage = strpos($current_route, 'booking') === 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurtainCall</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/layouts.css">
    <link rel="stylesheet" href="public/css/search.css">
    <link rel="stylesheet" href="public/css/auth.css">
    <link rel="stylesheet" href="public/css/breadcrumb.css">
    <link rel="stylesheet" href="public/css/play-details.css">
    <link rel="stylesheet" href="public/css/profile.css">
    <link rel="stylesheet" href="public/css/booking.css">
    <link rel="stylesheet" href="public/css/payment-success.css">
    <link rel="stylesheet" href="public/css/contact.css">
    <link rel="stylesheet" href="public/css/pagination.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/ckeditor5-builder-45.0.0/ckeditor5/ckeditor5.js"></script>

    <script src="public/js/auth-validation.js" defer></script>
    <script src="public/js/alert.js" defer></script>
    <script src="public/js/live-search.js" defer></script>
    <script src="public/js/schedule.js" defer></script>
</head>

<body class="<?= $isBookingPage ? 'booking-page' : '' ?>">
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="public/images/logo.png" alt="CurtainCall" class="navbar-logo">
                    <p class="brand-text text-white mb-0 ms-2">CurtainCall</p>
                </a>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title brand-text" id="offcanvasNavbarLabel">CurtainCall</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-left flex-grow-1 pe-3 gap-4">
                            <li class="nav-item">
                                <a class="nav-link <?= $current_route === 'home' ? 'active' : '' ?>" href="index.php">Trang chủ</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="index.php?route=play" role="button">
                                    Nhà hát kịch
                                </a>
                                <ul class="dropdown-menu">
                                    <?php if ($theaters_result && $theaters_result->num_rows > 0): ?>
                                        <?php while ($theater = $theaters_result->fetch_assoc()): ?>
                                            <li>
                                                <a class="dropdown-item" href="index.php?route=play&theater_id=<?= $theater['theater_id'] ?>">
                                                    <?= $theater['name'] ?>
                                                </a>
                                            </li>
                                        <?php endwhile; ?>
                                        <?php $theaters_result->data_seek(0);
                                        ?>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="#">Không có nhà hát kịch nào được tìm thấy.</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?route=contact">Liên hệ</a>
                            </li>
                        </ul>

                        <form id="live-search" class="d-flex search-container" role="search" method="GET" action="index.php">
                            <input type="hidden" name="route" value="search/index">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input
                                    id="search-input"
                                    class="form-control border-start-0 border-end-0 ps-0"
                                    type="search"
                                    name="query"
                                    placeholder="Bạn tìm gì hôm nay?"
                                    aria-label="Search"
                                    autocomplete="off">
                                <span class="input-group-text bg-white border-start-0">
                                    <span class="vr my-1 me-2 text-muted"></span>
                                    <button type="submit" class="btn p-0 bg-transparent">
                                        Tìm kiếm
                                    </button>
                                </span>
                            </div>
                            <div id="search-hints" class="search-hints d-none"></div>
                        </form>

                        <div class="login-btn mx-2">
                            <?php if (isset($_SESSION['user'])): ?>
                                <!-- User is logged in, show avatar and dropdown -->
                                <div class="dropdown">
                                    <a class="dropdown-toggle utility-btn d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php
                                        $avatarUrl = '';
                                        if (isset($_SESSION['user']['avatar']) && $_SESSION['user']['avatar']) {
                                            if (strpos($_SESSION['user']['avatar'], 'http') === 0) {
                                                $avatarUrl = $_SESSION['user']['avatar'];
                                            } else {
                                                $avatarUrl = BASE_URL . $_SESSION['user']['avatar'];
                                            }
                                        } else {
                                            // Default avatar
                                            $avatarUrl = BASE_URL . 'public/images/avatars/default.png';
                                        }
                                        ?>
                                        <img src="<?= $avatarUrl ?>" alt="User Avatar" class="user-avatar me-2">
                                        <span><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="index.php?route=user/profile"><i class="bi bi-person me-2"></i>Tài khoản</a></li>
                                        <li><a class="dropdown-item" href="index.php?route=booking/history"><i class="bi bi-ticket-perforated me-2"></i>Lịch sử đặt vé</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="index.php?route=user/logout"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <!-- User is not logged in, show login/register buttons -->
                                <button type="button" class="utility-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    Đăng nhập
                                </button>
                                <span class="login-divider vr my-2"></span>
                                <button type="button" class="utility-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    Đăng ký
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Notification System -->
    <?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
        <div class="notification-container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $_SESSION['error_message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>