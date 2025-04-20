<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Curtaincall</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="public/css/admin.css" rel="stylesheet">
    <link href="public/css/admin-dashboard.css" rel="stylesheet">
    <link href="public/css/pagination.css" rel="stylesheet">
    <link href="public/css/admin-allplays.css" rel="stylesheet">
    <?php if (strpos($_GET['route'] ?? '', 'viewPlay') !== false): ?>
    <link href="public/css/admin-viewplay.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (strpos($_GET['route'] ?? '', 'viewTheater') !== false): ?>
    <link href="public/css/admin-viewtheater.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (strpos($_GET['route'] ?? '', 'bookings') !== false): ?>
    <link href="public/css/admin-allbookings.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (strpos($_GET['route'] ?? '', 'viewBooking') !== false): ?>
    <link href="public/css/admin-viewbooking.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (strpos($_GET['route'] ?? '', 'users') !== false): ?>
    <link href="public/css/admin-allusers.css" rel="stylesheet">
    <?php endif; ?>
    <?php if (strpos($_GET['route'] ?? '', 'viewUser') !== false): ?>
    <link href="public/css/admin-viewuser.css" rel="stylesheet">
    <?php endif; ?>

    <link href="public/css/admin-alltheaters.css" rel="stylesheet">
    <link rel="shortcut icon" href="public/images/favicon.ico" type="image/x-icon">

    <?php
    $current_route = $_GET['route'] ?? '';
    // Include the CKEditor initialization script on pages that use rich text editing
    if (strpos($current_route, 'createPlay') !== false || 
        strpos($current_route, 'editPlay') !== false || 
        strpos($current_route, 'createTheater') !== false || 
        strpos($current_route, 'editTheater') !== false):
    ?>
    <script src="<?= BASE_URL ?>public/js/ckeditor-init.js"></script>
    <?php endif; ?>
    <script src="<?= BASE_URL ?>public/js/schedule-addremove.js"></script>
    <script src="<?= BASE_URL ?>public/js/sidebar.js" defer></script>
    </head>
<?php if (strpos($_GET['route'] ?? '', 'viewBooking') !== false): ?>
<body class="admin-viewbooking">
<?php elseif (strpos($_GET['route'] ?? '', 'viewUser') !== false): ?>
<body class="admin-viewuser">
<?php else: ?>
<body>
<?php endif; ?>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="public/images/logo.png" alt="CurtainCall" height="30" class="me-2">
                    <h3>CurtainCall</h3>
                </div>
                
            </div>
            <ul class="list-unstyled components">
                <li class="<?= strpos($_GET['route'] ?? '', 'dashboard') !== false ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?route=admin/dashboard">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="<?= strpos($_GET['route'] ?? '', 'plays') !== false || strpos($_GET['route'] ?? '', 'Play') !== false ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?route=admin/plays">
                        <i class="bi bi-film"></i> <span>Plays</span>
                    </a>
                </li>
                <li class="<?= strpos($_GET['route'] ?? '', 'theaters') !== false ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?route=admin/theaters">
                        <i class="bi bi-building"></i> <span>Theaters</span>
                    </a>
                </li>
                <li class="<?= strpos($_GET['route'] ?? '', 'bookings') !== false ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?route=admin/bookings">
                        <i class="bi bi-journal-check"></i> <span>Bookings</span>
                    </a>
                </li>
                <li class="<?= strpos($_GET['route'] ?? '', 'users') !== false ? 'active' : '' ?>">
                    <a href="<?= BASE_URL ?>index.php?route=admin/users">
                        <i class="bi bi-people"></i> <span>Users</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-primary">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="ms-auto d-flex">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> <?= $_SESSION['admin']['username'] ?? 'Admin' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?route=admin/profile"><i class="bi bi-person-gear"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?route=admin/logout"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Content Area -->
            <div class="container-fluid p-4">
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