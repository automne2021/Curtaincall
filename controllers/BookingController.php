<?php
require_once 'models/Play.php';
require_once 'models/Theater.php';
require_once 'models/Schedule.php';
require_once 'models/Seat.php';
require_once 'models/Booking.php';

class BookingController {
    private $conn;
    private $playModel;
    private $theaterModel;
    private $scheduleModel;
    private $seatModel;
    private $bookingModel;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
        $this->scheduleModel = new Schedule($conn);
        $this->seatModel = new Seat($conn);
        $this->bookingModel = new Booking($conn);
    }
    
    // Step 1: Show booking form with play details and schedule selection
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = 'index.php?route=booking/index';
            $_SESSION['error_message'] = 'Please log in to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Get play ID from query parameters
        $play_id = $_GET['play_id'] ?? null;
        
        if (!$play_id) {
            $_SESSION['error_message'] = 'Please select a play to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Get play details
        $play = $this->playModel->getPlayById($play_id);
        
        if (!$play) {
            $_SESSION['error_message'] = 'Play not found';
            header('Location: index.php');
            exit;
        }
        
        // Get theater details
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);
        
        // Get schedules for this play
        $schedules = $this->scheduleModel->getSchedulesByPlayId($play_id);
        
        // Include the view
        include 'views/layouts/header.php';
        include 'views/booking/index.php';
        include 'views/layouts/footer.php';
    }

    public function create() {
        $play_id = $_GET['play_id'] ?? null;
        
        if (!$play_id) {
            $_SESSION['error_message'] = 'Please select a play to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Redirect to the index method with the play_id
        header("Location: index.php?route=booking/index&play_id=$play_id");
        exit;
    }
    
    // Step 2: Select seats
    public function selectSeats() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Check if this is a return from confirmation page
        $isReturn = isset($_GET['return']) && $_GET['return'] === 'true';
        
        if ($isReturn) {
            // We're returning from confirmation page, use session data
            $play_id = $_SESSION['booking_details']['play_id'];
            $schedule_date = $_SESSION['booking_details']['schedule_date'];
            $schedule_time = $_SESSION['booking_details']['schedule_time'];
        } else {
            // Normal form submission
            $play_id = $_POST['play_id'] ?? null;
            $schedule_date = $_POST['schedule_date'] ?? null;
            $schedule_time = $_POST['schedule_time'] ?? null;
            
            // Store booking details in session for next step
            $_SESSION['booking_details'] = [
                'play_id' => $play_id,
                'schedule_date' => $schedule_date,
                'schedule_time' => $schedule_time
            ];
        }
        
        if (!$play_id || !$schedule_date || !$schedule_time) {
            $_SESSION['error_message'] = 'Please select a valid schedule';
            header('Location: index.php?route=booking/index&play_id=' . $play_id);
            exit;
        }
        
        // Get play details
        $play = $this->playModel->getPlayById($play_id);
        
        // Get theater details
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);
        
        // Get seat map for this theater
        $seatMap = $this->seatModel->getSeatMapByTheater($play['theater_id']);
        
        // Get seat availability for this play and schedule
        $seatAvailability = $this->seatModel->getSeatAvailability($play_id, $schedule_date, $schedule_time);
        
        // Get seat prices
        $seatPrices = $this->seatModel->getSeatPrices($play['theater_id']);
        
        // Include the view
        include 'views/layouts/header.php';
        include 'views/booking/select_seats.php';
        include 'views/layouts/footer.php';
    }
    
    // Step 3: Confirm booking
    public function confirm() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Get selected seats from form
        $selected_seats = $_POST['seats'] ?? [];
        
        if (empty($selected_seats)) {
            $_SESSION['error_message'] = 'Please select at least one seat';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        
        // Get booking details from session
        $booking_details = $_SESSION['booking_details'] ?? null;
        
        if (!$booking_details) {
            $_SESSION['error_message'] = 'Invalid booking session';
            header('Location: index.php');
            exit;
        }
        
        $play_id = $booking_details['play_id'];
        
        // Get play details
        $play = $this->playModel->getPlayById($play_id);
        
        // Get theater details
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);
        
        // Get seat details and calculate total price
        $selectedSeatsInfo = $this->seatModel->getSelectedSeatsInfo($play['theater_id'], $selected_seats);
        $totalPrice = 0;
        
        foreach ($selectedSeatsInfo as $seat) {
            $totalPrice += $seat['price'];
        }
        
        // Store seats in session for final step
        $_SESSION['booking_details']['selected_seats'] = $selected_seats;
        $_SESSION['booking_details']['total_price'] = $totalPrice;
        
        // Include the view
        include 'views/layouts/header.php';
        include 'views/booking/confirm.php';
        include 'views/layouts/footer.php';
    }
    
    // Step 4: Complete booking
    public function complete() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to book tickets';
            header('Location: index.php');
            exit;
        }
        
        // Get booking details from session
        $booking_details = $_SESSION['booking_details'] ?? null;
        
        if (!$booking_details) {
            $_SESSION['error_message'] = 'Invalid booking session';
            header('Location: index.php');
            exit;
        }
        
        $user_id = $_SESSION['user']['user_id'];
        $play_id = $booking_details['play_id'];
        $selected_seats = $booking_details['selected_seats'];
        
        // Get play details
        $play = $this->playModel->getPlayById($play_id);
        $theater_id = $play['theater_id'];
        
        // Set expiration time (e.g., 15 minutes from now)
        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Create bookings for each selected seat
        $success = true;
        foreach ($selected_seats as $seat_id) {
            $seat_price = $this->seatModel->getSeatPrice($theater_id, $seat_id);
            $result = $this->bookingModel->createBooking(
                $user_id,
                $play_id,
                $theater_id,
                $seat_id,
                $expires_at,
                $seat_price
            );
            
            if (!$result) {
                $success = false;
                break;
            }
            
            // Update seat status to Pending
            $this->seatModel->updateSeatStatus($play_id, $theater_id, $seat_id, 'Pending');
        }
        
        if ($success) {
            // Clear booking session data
            unset($_SESSION['booking_details']);
            
            $_SESSION['success_message'] = 'Booking successful! Please complete payment within 15 minutes to secure your tickets.';
            header('Location: index.php?route=booking/history');
            exit;
        } else {
            $_SESSION['error_message'] = 'There was an error processing your booking. Please try again.';
            header('Location: index.php?route=booking/index&play_id=' . $play_id);
            exit;
        }
    }
    
    // View booking history
    public function history() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to view booking history';
            header('Location: index.php');
            exit;
        }
        
        $user_id = $_SESSION['user']['user_id'];
        
        // Call the cleanup method BEFORE getting the bookings
        $this->cleanupExpiredBookings();
        
        // Get user's booking history
        $bookings = $this->bookingModel->getBookingsByUserId($user_id);
        
        // Include the view
        include 'views/layouts/header.php';
        include 'views/booking/history.php';
        include 'views/layouts/footer.php';
    }

    private function cleanupExpiredBookings() {
        // Get all expired pending bookings
        $expired_bookings = $this->bookingModel->getExpiredBookings();
        
        if (!empty($expired_bookings)) {
            foreach ($expired_bookings as $booking) {
                // Update seat status back to Available
                $this->seatModel->updateSeatStatus(
                    $booking['play_id'],
                    $booking['theater_id'],
                    $booking['seat_id'],
                    'Available'
                );
                
                // Update booking status to Expired
                $this->bookingModel->updateBookingStatus($booking['booking_id'], 'Expired');
            }
        }
    }

    public function cancel() {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['error_message'] = 'Please log in to cancel bookings';
            header('Location: index.php');
            exit;
        }
        
        $booking_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user']['user_id'];
        
        if (!$booking_id) {
            $_SESSION['error_message'] = 'Invalid booking ID';
            header('Location: index.php?route=booking/history');
            exit;
        }
        
        // Get booking details
        $booking = $this->bookingModel->getBookingById($booking_id);
        
        if (!$booking || $booking['user_id'] != $user_id) {
            $_SESSION['error_message'] = 'Booking not found or you are not authorized to cancel it';
            header('Location: index.php?route=booking/history');
            exit;
        }
        
        // Allow cancellation of pending or expired bookings
        if ($booking['status'] !== 'Pending' && $booking['status'] !== 'Expired') {
            $_SESSION['error_message'] = 'Only pending or expired bookings can be cancelled/removed';
            header('Location: index.php?route=booking/history');
            exit;
        }
        
        // Only update seat status if booking is still pending (expired seats should already be Available)
        if ($booking['status'] === 'Pending') {
            // Update seat status back to Available
            $this->seatModel->updateSeatStatus(
                $booking['play_id'], 
                $booking['theater_id'], 
                $booking['seat_id'], 
                'Available'
            );
        }
        
        // Delete the booking
        $success = $this->bookingModel->deleteBooking($booking_id);
        
        if ($success) {
            if ($booking['status'] === 'Pending') {
                $_SESSION['success_message'] = 'Booking cancelled successfully';
            } else {
                $_SESSION['success_message'] = 'Expired booking removed successfully';
            }
        } else {
            $_SESSION['error_message'] = 'Failed to process your request';
        }
        
        header('Location: index.php?route=booking/history');
        exit;
    }
}