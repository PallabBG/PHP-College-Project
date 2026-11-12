<?php
// student/enroll.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];
global $conn;

// Check if course exists
$query = "SELECT id FROM courses WHERE id = '$course_id'";
$result = mysqli_query($conn, $query);
if (!mysqli_fetch_row($result)) {
    set_flash_message('error', 'Course not found.');
    redirect('courses.php');
}

// Check if already enrolled
$query = "SELECT id FROM enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'";
$result = mysqli_query($conn, $query);
if (mysqli_fetch_row($result)) {
    set_flash_message('success', 'You are already enrolled in this course.');
    redirect('course.php?id=' . $course_id);
}

// Enroll
$query = "INSERT INTO enrollments (student_id, course_id) VALUES ('$student_id', '$course_id')";
if (mysqli_query($conn, $query)) {
    // Initialize progress
    $query = "INSERT INTO course_progress (student_id, course_id, progress_percentage) VALUES ('$student_id', '$course_id', 0)";
    mysqli_query($conn, $query);
    
    set_flash_message('success', 'Successfully enrolled in the course!');
    redirect('course.php?id=' . $course_id);
} else {
    set_flash_message('error', 'Failed to enroll in the course.');
    redirect('courses.php');
}
?>
