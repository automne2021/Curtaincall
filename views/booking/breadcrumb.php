<?php
// Determine which step is active based on the current route
$current_route = $_GET['route'] ?? '';
?>

<div class="booking-breadcrumb">
    <div class="breadcrumb-container">
        <!-- Step 1: Select Seats -->
        <div class="breadcrumb-step <?= strpos($_SERVER['REQUEST_URI'], 'selectSeats') !== false ? 'active' : '' ?> <?= strpos($_SERVER['REQUEST_URI'], 'confirm') !== false || strpos($_SERVER['REQUEST_URI'], 'complete') !== false ? 'completed' : '' ?>">
            <div class="step-icon">
                <i class="bi bi-chair"></i>
                <div class="step-check">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>
            <div class="step-label">Chọn chỗ</div>
        </div>
        
        <div class="breadcrumb-connector <?= strpos($_SERVER['REQUEST_URI'], 'confirm') !== false || strpos($_SERVER['REQUEST_URI'], 'complete') !== false ? 'active' : '' ?>"></div>
        
        <!-- Step 2: Payment -->
        <div class="breadcrumb-step <?= strpos($_SERVER['REQUEST_URI'], 'confirm') !== false ? 'active' : '' ?> <?= strpos($_SERVER['REQUEST_URI'], 'complete') !== false ? 'completed' : '' ?>">
            <div class="step-icon">
                <i class="bi bi-credit-card"></i>
                <div class="step-check">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>
            <div class="step-label">Thanh toán</div>
        </div>
    </div>
</div>