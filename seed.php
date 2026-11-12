<?php
// seed.php
require_once 'config/database.php';

$pdo = getDB();

try {
    $pdo->beginTransaction();

    // 1. Clear existing data
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE course_progress");
    $pdo->exec("TRUNCATE TABLE lecture_progress");
    $pdo->exec("TRUNCATE TABLE assignment_submissions");
    $pdo->exec("TRUNCATE TABLE assignments");
    $pdo->exec("TRUNCATE TABLE quiz_attempts");
    $pdo->exec("TRUNCATE TABLE quiz_questions");
    $pdo->exec("TRUNCATE TABLE quizzes");
    $pdo->exec("TRUNCATE TABLE materials");
    $pdo->exec("TRUNCATE TABLE lectures");
    $pdo->exec("TRUNCATE TABLE enrollments");
    $pdo->exec("TRUNCATE TABLE courses");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // 2. Create users
    $password = password_hash('password123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    
    // Instructor
    $stmt->execute(['John Doe', 'instructor@example.com', $password, 'instructor']);
    $instructor_id = $pdo->lastInsertId();
    
    // Student
    $stmt->execute(['Jane Smith', 'student@example.com', $password, 'student']);
    $student_id = $pdo->lastInsertId();

    // 3. Create course
    $stmt = $pdo->prepare("INSERT INTO courses (instructor_id, title, description, category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$instructor_id, 'Introduction to PHP & MySQL', 'Learn the basics of PHP and MySQL by building real world projects.', 'Web Development']);
    $course_id = $pdo->lastInsertId();

    // 4. Enroll student
    $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    $stmt->execute([$student_id, $course_id]);

    $stmt = $pdo->prepare("INSERT INTO course_progress (student_id, course_id, progress_percentage) VALUES (?, ?, 0)");
    $stmt->execute([$student_id, $course_id]);

    // 5. Add Quizzes
    $stmt = $pdo->prepare("INSERT INTO quizzes (course_id, title, description, total_marks) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, 'PHP Basics Quiz', 'Test your knowledge on PHP fundamentals.', 2]);
    $quiz_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quiz_id, 'What does PHP stand for?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Private Home Page', 'Processed Hypertext Page', 'B', 1]);
    $stmt->execute([$quiz_id, 'Which symbol is used to declare a variable in PHP?', '$', '@', '&', '%', 'A', 1]);

    // 6. Add Assignments
    $stmt = $pdo->prepare("INSERT INTO assignments (course_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$course_id, 'Build a simple Calculator', 'Write a PHP script that performs addition, subtraction, multiplication, and division.', '2026-12-31']);

    $pdo->commit();
    echo "<h1>Demo data seeded successfully!</h1>";
    echo "<p>Instructor Login: instructor@example.com / password123</p>";
    echo "<p>Student Login: student@example.com / password123</p>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed to seed database: " . $e->getMessage();
}
?>
