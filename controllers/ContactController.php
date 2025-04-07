<?php
class ContactController {
    public function __construct($conn) {
        $this->conn = $conn;
        // Load theater model for navigation
        require_once 'models/Theater.php';
        $this->theaterModel = new Theater($conn);
    }
    
    public function index() {
        // Get theaters for the navigation menu
        $theaters_result = $this->theaterModel->getAllTheaters();
        
        // Load the views
        include 'views/layouts/header.php';
        include 'views/contact/index.php';
        include 'views/layouts/footer.php';
    }
    
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Basic validation
            $errors = [];
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';
            
            if (empty($name)) {
                $errors['name'] = 'Please enter your name';
            }
            
            if (empty($email)) {
                $errors['email'] = 'Please enter your email';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email';
            }
            
            if (empty($subject)) {
                $errors['subject'] = 'Please enter a subject';
            }
            
            if (empty($message)) {
                $errors['message'] = 'Please enter your message';
            }
            
            if (!empty($errors)) {
                $_SESSION['contact_errors'] = $errors;
                $_SESSION['contact_data'] = [
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject,
                    'message' => $message
                ];
                header('Location: index.php?route=contact');
                exit;
            }
            
            // Send email (in a real app, you would use a proper mail library)
            $to = 'support@curtaincall.com'; // Replace with your actual email
            $email_subject = "Contact Form: $subject";
            $email_body = "You have received a new message from your website contact form.\n\n";
            $email_body .= "Name: $name\n";
            $email_body .= "Email: $email\n\n";
            $email_body .= "Message:\n$message\n";
            
            $headers = "From: noreply@curtaincall.com\n";
            $headers .= "Reply-To: $email";
            
            // For development, just save to a file instead of sending email
            file_put_contents('contact_messages.txt', $email_body . "\n\n", FILE_APPEND);
            
            // You can uncomment this in production
            // mail($to, $email_subject, $email_body, $headers);
            
            $_SESSION['success_message'] = 'Thank you for your message! We will get back to you soon.';
            header('Location: index.php?route=contact');
            exit;
        } else {
            header('Location: index.php?route=contact');
            exit;
        }
    }
}