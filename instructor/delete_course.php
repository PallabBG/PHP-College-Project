<?php
// instructor/delete_course.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership
$query = "SELECT id FROM courses WHERE id = '$course_id' AND instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
if (mysqli_fetch_assoc($result)) {
    $query = "DELETE FROM courses WHERE id = '$course_id'";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', 'Course deleted successfully.');
    } else {
        set_flash_message('error', 'Failed to delete course.');
    }
} else {
    set_flash_message('error', 'Course not found or permission denied.');
}

redirect('courses.php');
?>
