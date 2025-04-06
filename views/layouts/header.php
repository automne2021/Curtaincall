<?php
// filepath: c:\Users\VY\Downloads\curtaincall\views\layouts\header.php
// Get current route for active nav highlighting
$current_route = $_GET['route'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurtainCall</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/layouts.css">
    <link rel="stylesheet" href="public/css/auth.css">
</head>

<body>
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
                                <a class="nav-link <?= $current_route === 'home' ? 'active' : '' ?>" href="index.php">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="index.php?route=play" role="button">
                                    Theaters
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
                                        <?php $theaters_result->data_seek(0); // Reset result pointer 
                                        ?>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="#">No theaters available</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?route=contact">Contact</a>
                            </li>
                        </ul>

                        <form class="d-flex" role="search" method="GET" action="index.php">
                            <input type="hidden" name="route" value="play/search">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input
                                    class="form-control border-start-0 border-end-0 ps-0"
                                    type="search"
                                    name="query"
                                    placeholder="Bạn tìm gì hôm nay?"
                                    aria-label="Search">
                                <span class="input-group-text bg-white border-start-0">
                                    <span class="vr my-1 me-2 text-muted"></span>
                                    <button type="submit" class="btn p-0 bg-transparent">
                                        Tìm kiếm
                                    </button>
                                </span>
                            </div>
                        </form>

                        <div class="login-btn mx-2">
                            <button type="button" class="utility-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Đăng nhập
                            </button>
                            <span class="login-divider vr my-2"></span>
                            <button type="button" class="utility-btn" data-bs-toggle="modal" data-bs-target="#registerModal">
                                Đăng ký
                            </button>
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