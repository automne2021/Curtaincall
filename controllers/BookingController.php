<?php
require_once 'models/Play.php';
require_once 'models/Theater.php';
require_once 'models/Schedule.php';
require_once 'models/Seat.php';
require_once 'models/Booking.php';
require_once 'models/User.php';

class BookingController
{
    private $conn;
    private $playModel;
    private $theaterModel;
    private $scheduleModel;
    private $seatModel;
    private $bookingModel;
    private $userModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->playModel = new Play($conn);
        $this->theaterModel = new Theater($conn);
        $this->scheduleModel = new Schedule($conn);
        $this->seatModel = new Seat($conn);
        $this->bookingModel = new Booking($conn);
        $this->userModel = new User($conn);
    }

    // Helper method to check if admin is logged in
    private function checkAdminAuth() {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=admin/login');
            exit;
        }
    }
    
    // List all bookings
    public function bookings() {
        $this->checkAdminAuth();
        
        // Get current page from query string, default to 1 if not set
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $per_page = 10;
        
        // Get paginated bookings
        $result = $this->bookingModel->getPaginatedBookings($page, $per_page);
        $bookings = $result['bookings'];
        $pagination = $result['pagination'];
        
        // Set base URL for pagination
        $base_url = BASE_URL . 'index.php?route=admin/bookings';
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/bookings/bookings.php';
        include 'views/admin/layouts/footer.php';
    }
    
    // View a single booking
    public function viewBooking() {
        $this->checkAdminAuth();
        
        $booking_id = $_GET['id'] ?? null;
        if (!$booking_id) {
            $_SESSION['error_message'] = 'No booking ID specified';
            header('Location: index.php?route=admin/bookings');
            exit;
        }
        
        $booking = $this->bookingModel->getBookingDetailsById($booking_id);
        if (!$booking) {
            $_SESSION['error_message'] = 'Booking not found';
            header('Location: index.php?route=admin/bookings');
            exit;
        }
        
        // Get user details
        $user = $this->userModel->getUserById($booking['user_id']);
        
        // Get theater details
        $theater = $this->theaterModel->getTheaterById($booking['theater_id']);
        
        // Get seat map for this theater
        $seatMap = $this->seatModel->getSeatMapByTheater($booking['theater_id']);
        
        // Get seat prices by type for display
        $seatPrices = $this->seatModel->getSeatPrices($booking['theater_id']);
        
        // Create a mapping of seat types to prices for display
        $seatTypes = [];
        foreach ($seatPrices as $type => $price) {
            $seatTypes[$type] = $price;
        }
        
        // Get all booked seats for this play (to show on the seat map)
        $bookedSeats = $this->bookingModel->getBookedSeatsByPlay($booking['play_id']);
        
        include 'views/admin/layouts/header.php';
        include 'views/admin/bookings/viewBooking.php';
        include 'views/admin/layouts/footer.php';
    }

    // Step 1: Show booking form with play details and schedule selection
    public function index()
    {
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
    public function selectSeats()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = 'index.php?route=play/view&play_id=' . ($_POST['play_id'] ?? 0) . '#scheduleSection';
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để đặt vé';
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
            $schedule_time = $_POST['schedule_time'] ?? null;

            if (!$play_id || !$schedule_time) {
                $_SESSION['error_message'] = 'Vui lòng chọn lịch diễn để đặt vé';
                header('Location: index.php?route=play/view&play_id=' . $play_id . '#scheduleSection');
                exit;
            }

            // Parse the composite value from schedule_time (format: play_id_timestamp)
            list($schedule_play_id, $timestamp) = explode('_', $schedule_time);
            
            // Convert timestamp back to datetime format
            $schedule_date_part = date('Y-m-d', $timestamp);
            $schedule_time_part = date('H:i:s', $timestamp);
            
            // Get schedule details using play_id, date and time
            $sql = "SELECT * FROM schedules WHERE play_id = ? AND date = ? AND start_time = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $play_id, $schedule_date_part, $schedule_time_part);
            $stmt->execute();
            $schedule = $stmt->get_result()->fetch_assoc();

            if (!$schedule || $schedule['play_id'] != $play_id) {
                $_SESSION['error_message'] = 'Lịch chiếu không hợp lệ';
                header('Location: index.php?route=play/view&play_id=' . $play_id . '#scheduleSection');
                exit;
            }

            // Store booking details in session for next step
            $_SESSION['booking_details'] = [
                'play_id' => $play_id,
                'schedule_date' => $schedule_date_part,
                'schedule_time' => $schedule['start_time'],
                'timestamp' => $timestamp
            ];
        }

        // Get play details
        $play = $this->playModel->getPlayById($play_id);

        // Get theater details
        $theater = $this->theaterModel->getTheaterById($play['theater_id']);

        // Get seat map for this theater
        $seatMap = $this->seatModel->getSeatMapByTheater($play['theater_id']);

        // Get seat availability for this play and schedule
        $seatAvailability = $this->seatModel->getSeatAvailability(
            $play_id, 
            $_SESSION['booking_details']['schedule_date'], 
            $_SESSION['booking_details']['timestamp']
        );

        // Get seat prices
        $seatPrices = $this->seatModel->getSeatPrices($play['theater_id']);

        // Include the view
        include 'views/layouts/header.php';
        include 'views/booking/select_seats.php';
        include 'views/layouts/footer.php';
    }

    public function cancelConfirmation() {
        // Get play ID from session
        $play_id = $_SESSION['booking_details']['play_id'] ?? null;
        
        // Clear booking session
        unset($_SESSION['booking_details']);
        
        // Set confirmation message
        $_SESSION['info_message'] = 'Đặt vé đã bị hủy. Bạn có thể tiếp tục duyệt các vở kịch khác.';
        
        // Redirect back to play details
        if ($play_id) {
            header('Location: index.php?route=play/view&play_id=' . $play_id);
        } else {
            header('Location: index.php');
        }
        exit;
    }

    // Step 3: Confirm booking
    public function confirm()
    {
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
            header('Location: index.php?route=user/login');
            exit;
        }
    
        // Get booking details from session
        $booking_details = $_SESSION['booking_details'] ?? null;
    
        if (!$booking_details) {
            $_SESSION['error_message'] = 'No booking information found';
            header('Location: index.php');
            exit;
        }
    
        $user_id = $_SESSION['user']['user_id'];
        $play_id = $booking_details['play_id'];
        $selected_seats = $booking_details['selected_seats'];
        $schedule_date = $booking_details['schedule_date']; // Get the scheduled date
    
        // Get play details
        $play = $this->playModel->getPlayById($play_id);
        $theater_id = $play['theater_id'];
    
        // Set expiration time (e.g., 15 minutes from now)
        $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    
        // Create bookings for each selected seat
        $success = true;
        $booking_ids = [];
        
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            foreach ($selected_seats as $seat_id) {
                // Get price for this seat
                $price = $this->seatModel->getSeatPrice($theater_id, $seat_id);
                
                // Create booking record
                $booking_result = $this->bookingModel->createBooking(
                    $user_id, 
                    $play_id, 
                    $theater_id, 
                    $seat_id, 
                    $expires_at,
                    $price,
                    $schedule_date  // Pass the schedule date to the createBooking method
                );
                
                if (!$booking_result) {
                    $success = false;
                    break;
                }
                
                $booking_id = $this->conn->insert_id;
                $booking_ids[] = $booking_id;
                
                // Update seat status to 'Pending'
                $this->seatModel->updateSeatStatus($play_id, $theater_id, $seat_id, 'Pending');
            }
            
            // If all bookings were created successfully
            if ($success) {
                // For demonstration purposes, set all bookings to 'Paid' status
                foreach ($booking_ids as $id) {
                    $this->bookingModel->updateBookingStatus($id, 'Paid');
                }
                
                // Commit transaction
                $this->conn->commit();
                
                // Clear booking session data
                unset($_SESSION['booking_details']);
                
                // Redirect to booking history with success message
                $_SESSION['success_message'] = 'Payment successful! Your tickets are now confirmed.';
                header('Location: index.php?route=booking/history&payment_success=1&booking_id=' . $booking_ids[0]);
                exit;
            } else {
                // Rollback transaction if any booking failed
                $this->conn->rollback();
                $_SESSION['error_message'] = 'Failed to complete booking';
                header('Location: index.php?route=booking/selectSeats&return=true');
                exit;
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            header('Location: index.php?route=booking/selectSeats&return=true');
            exit;
        }
    }

    // View booking history
    public function history()
    {
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

    private function cleanupExpiredBookings()
    {
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
}
