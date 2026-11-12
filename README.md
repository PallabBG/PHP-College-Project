# EduPortal Academy - PHP & MySQL E-Learning Platform

This project is a traditional server-rendered E-Learning Platform built with HTML5, CSS3, Vanilla JavaScript, PHP 8+, and MySQL.

## Features
- **User Roles:** Student, Instructor, Admin.
- **Instructor Dashboard:** Create courses, upload video lectures and materials, create quizzes, assign homework, and track student progress.
- **Student Dashboard:** Browse available courses, enroll, watch video lectures, download course materials, take quizzes, submit assignments, and track course progress visually.
- **Authentication:** Secure session-based authentication with `password_hash()` and `password_verify()`.
- **Database Access:** Uses PDO with prepared statements to prevent SQL injection.

## Folder Structure
- `/assets`: Contains CSS (`style.css`, `dashboard.css`, etc.) and JavaScript files.
- `/config`: Contains `database.php` for PDO connection.
- `/includes`: Contains reusable partials like `header.php`, `navbar.php`, `sidebar.php`, and `functions.php`.
- `/uploads`: Stores user-uploaded videos, documents, assignment submissions, and thumbnails.
- `/auth`: Contains login, register, and logout logic.
- `/student`: Student-facing pages (dashboard, course viewer, quizzes, progress).
- `/instructor`: Instructor-facing pages (course management, student tracking).
- `/admin`: Basic admin dashboard.

## Requirements
- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB
- XAMPP / WAMP / Laragon (for local development)

## Setup Instructions (XAMPP)
1. Place the `elearning-platform` (or project root folder) inside your `C:/xampp/htdocs/` directory.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Create a new database named `elearning_platform`.
5. Import the `database.sql` file provided in the root directory.
6. Open `config/database.php` and verify the database credentials (default is `root` with no password).
7. Access the platform at `http://localhost/elearning-platform/`.

## Demo Data
To populate the database with sample data, visit `http://localhost/elearning-platform/seed.php` once in your browser. 
It will create:
- **Instructor Login:** instructor@example.com / password123
- **Student Login:** student@example.com / password123
