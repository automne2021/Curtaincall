<?php
// Ensure admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: index.php?route=admin/login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CurtainCall Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <!-- Sidebar -->
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="index.php?route=admin/dashboard" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">CurtainCall Admin</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                        <li class="nav-item">
                            <a href="index.php?route=admin/dashboard" class="nav-link align-middle px-0 text-white">
                                <i class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/plays" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-camera-reels"></i> <span class="ms-1 d-none d-sm-inline">Plays</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/theaters" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-building"></i> <span class="ms-1 d-none d-sm-inline">Theaters</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/schedules" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-calendar3"></i> <span class="ms-1 d-none d-sm-inline">Schedules</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/users" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/bookings" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-ticket-perforated"></i> <span class="ms-1 d-none d-sm-inline">Bookings</span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?route=admin/seat-prices" class="nav-link px-0 align-middle text-white">
                                <i class="fs-4 bi-tags"></i> <span class="ms-1 d-none d-sm-inline">Seat Prices</span>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown pb-4">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4"></i>
                            <span class="d-none d-sm-inline mx-1"><?= htmlspecialchars($_SESSION['admin']['username']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                            <li><a class="dropdown-item" href="index.php?route=admin/logout">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Content -->
            <div class="col py-3">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>