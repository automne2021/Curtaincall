<?php
class PaymentController {
    private $bookingModel;
    private $userModel;
    
    public function __construct($conn) {
        require_once 'models/Booking.php';
        require_once 'models/User.php';
        $this->bookingModel = new Booking($conn);
        $this->userModel = new User($conn);
    }
    
    public function process() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to complete payment';
            header('Location: index.php?route=user/login');
            exit;
        }
        
        $booking_id = $_GET['booking_id'] ?? null;
        $user_id = $_SESSION['user']['user_id'];
        
        if (!$booking_id) {
            $_SESSION['error_message'] = 'Invalid booking ID';
            header('Location: index.php?route=booking/history');
            exit;
        }
        
        // Verify booking belongs to user
        $booking = $this->bookingModel->getBookingById($booking_id);
        if (!$booking || $booking['user_id'] != $user_id) {
            $_SESSION['error_message'] = 'Booking not found or unauthorized';
            header('Location: index.php?route=booking/history');
            exit;
        }
        
        // Simple payment simulation - always succeeds
        // In a real app, you'd integrate with a payment gateway
        
        // Update booking status to Paid
        $success = $this->bookingModel->updateBookingStatus($booking_id, 'Paid');
        
        if ($success) {
            $_SESSION['success_message'] = 'Payment successful! Your tickets are confirmed.';
            $_SESSION['payment_success'] = true;
            header('Location: index.php?route=booking/history&payment_success=1');
        } else {
            $_SESSION['error_message'] = 'Payment processing failed. Please try again.';
            header('Location: index.php?route=booking/history');
        }
        exit;
    }
}