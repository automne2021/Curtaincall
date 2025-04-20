# CurtainCall - Online Theater Ticket Booking System

## Overview

CurtainCall is an online platform designed for theater enthusiasts to browse, discover, and book tickets for theatrical performances. The system offers a user-friendly interface for customers to explore various theaters, view upcoming plays, select seats, and complete bookings. Additionally, it provides a comprehensive admin dashboard for managing plays, theaters, bookings, and users.

## Features

### User Features

- Browse plays by theater or view all upcoming performances
- Advanced filtering and sorting options (by date, price, name)
- Detailed play information with schedule, theater details, and descriptions
- Seat selection with interactive seating map
- Secure booking and payment process
- User account management (profile, booking history)
- Social login integration with Google

### Admin Features

- Comprehensive dashboard with analytics and statistics
- Play management (create, edit, view, delete)
- Theater management (add venues, manage seating arrangements)
- Booking management and monitoring
- User management and oversight
- Content management with rich text editor

## Technical Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Backend**: PHP (OOP approach)
- **Database**: MySQL
- **Rich Text Editor**: CKEditor 5
- **Additional Libraries**: Chart.js for analytics

## Project Structure

- `controllers`: Contains controller classes for handling requests
- `models`: Database models for data manipulation
- `views`: UI templates separated by functionality
- `public`: Static assets (CSS, JavaScript, images)
- `config`: Database and application configuration
- `api`: API endpoints for AJAX requests
- `helpers`: Utility functions

## Installation

### Prerequisites

- XAMPP (or similar stack with PHP 7.4+ and MySQL)
- Web browser
- MySQL database

### Setup Instructions

1. **Clone the repository or extract the project files**

   ```
   git clone https://github.com/automne2021/Curtaincall.git
   ```

   Or extract the ZIP file to your htdocs folder in XAMPP.

2. **Set up the database**

   - Start Apache and MySQL services in XAMPP Control Panel
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `ticket_booking`
   - Import the SQL file `ticket_booking.sql` from the project root

3. **Configure database connection**

   - Open `database.php`
   - Verify database credentials match your setup (default uses root with no password)

4. **Run the application**
   - Access the website at: http://localhost/Curtaincall/

## Access Information

### User Access

- **URL**: http://localhost/Curtaincall/
- **Demo Account**:
  - Username: user1
  - Password: abcd1234@
- Alternatively, you can register a new account or use Google login

### Admin Access

- **URL**: http://localhost/Curtaincall/admin.php
- **Default Credentials**:
  - Username: admin
  - Password: admin123

## Usage Guide

### For Users

1. Browse plays from the homepage or navigate to specific theaters
2. Click on a play to view details and schedule
3. Select a date and time for the performance
4. Choose seats from the seating chart
5. Review booking summary and complete payment
6. Access booking history and e-tickets from your profile

### For Administrators

1. Log in to the admin panel
2. Use the dashboard to monitor site activity and statistics
3. Manage plays, theaters, and seating arrangements
4. View and manage bookings
5. Access user management to handle customer accounts

## Known Limitations

- Payment processing is simulated (no real payment gateway integration)
- Limited to Vietnamese language interface
- Optimized for modern browsers (Chrome, Firefox, Safari, Edge)

## Dependencies and Setup

### Composer Dependencies

This project uses Composer to manage PHP dependencies. If you encounter errors related to missing vendor files:

1. Install Composer:

   - Download from [getcomposer.org](https://getcomposer.org/download/)
   - Follow installation instructions for your operating system

2. Install dependencies:

   ```bash
   cd /path/to/Curtaincall
   composer require google/apiclient:"^2.0"
   ```

3. If you encounter memory limit issues:

   ```bash
   php -d memory_limit=-1 /path/to/composer.phar install
   ```

4. Update dependencies:
   ```bash
   composer update
   ```

The vendor directory contains essential libraries and should be properly initialized before running the application.
