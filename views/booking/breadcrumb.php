<?php
// Determine which step is active based on the current route
$current_route = $_GET['route'] ?? '';
$step = 1; // Default to step 1

if ($current_route == 'booking/selectSeats') {
    $step = 2;
} elseif ($current_route == 'booking/confirm') {
    $step = 3;
}
?>

<div class="booking-breadcrumb">
    <div class="breadcrumb-container">
        <div class="breadcrumb-step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
            <div class="step-icon">
                <i class="bi bi-calendar-event"></i>
                <span class="step-check"><i class="bi bi-check-lg"></i></span>
            </div>
            <div class="step-label">Select Schedule</div>
        </div>
        <div class="breadcrumb-connector <?= $step > 1 ? 'active' : '' ?>"></div>
        
        <div class="breadcrumb-step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
            <div class="step-icon">
                <!-- Changed from bi-chair to bi-grid which is more widely available -->
                <i class="bi bi-grid"></i>
                <span class="step-check"><i class="bi bi-check-lg"></i></span>
            </div>
            <div class="step-label">Select Seats</div>
        </div>
        <div class="breadcrumb-connector <?= $step > 2 ? 'active' : '' ?>"></div>
        
        <div class="breadcrumb-step <?= $step >= 3 ? 'active' : '' ?>">
            <div class="step-icon">
                <i class="bi bi-credit-card"></i>
                <span class="step-check"><i class="bi bi-check-lg"></i></span>
            </div>
            <div class="step-label">Confirm & Pay</div>
        </div>
    </div>
</div>