# Fishing Port Management System

## Overview
The Fishing Port Management System is a web-based application designed to streamline and digitize the operations of a fishing port. It enables efficient management of boats, fishermen, workers, and customer interactions, automating attendance, trip tracking, and arrival updates.

## Features
- Fisherman registration and authentication
- Boat registration and approval
- Worker (driver/worker) management
- Marking attendance for trips (departure and arrival)
- Customer order placement and tracking
- Admin dashboard for approvals, analytics, and management
- Notifications and feedback system
- Data backup and restore

## Technologies Used
- PHP (Backend)
- MySQL (Database)
- HTML, CSS, JavaScript (Frontend)
- Font Awesome, Google Fonts (UI Enhancements)

## Setup Instructions
1. **Requirements:**
   - PHP 7.x or higher
   - MySQL 5.x or higher
   - Web server (e.g., Apache, WAMP, XAMPP)

2. **Installation:**
   - Clone or copy the project files to your web server directory (e.g., `c:/wamp64/www/basicstructure`).
   - Import the provided SQL file (e.g., `fishing_port_YYYY-MM-DD.sql`) into your MySQL database.
   - Update database credentials in PHP files if needed.

3. **Running the Application:**
   - Start your web server and MySQL service.
   - Open `index.html` in your browser to access the application.

## Folder Structure
- `assets/` - Images, CSS, JS, and fonts
- `uploads/` - Uploaded files and documents
- `demoform/` - Demo forms and captcha
- Main PHP/HTML files for each module (attendance, registration, dashboard, etc.)

## User Roles
- **Admin:** Manages users, boats, approvals, analytics, and system data.
- **Fisherman:** Registers boats, manages workers, marks attendance, and updates trip details.
- **Customer:** Places orders, tracks status, and provides feedback.

## Contribution
Feel free to fork this repository and submit pull requests for improvements or bug fixes.

## License
This project is for educational and demonstration purposes.

## Contact
For support or questions, please use the contact form in the application or email the project maintainer.
