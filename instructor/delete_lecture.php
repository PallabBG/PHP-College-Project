<?php
// instructor/delete_lecture.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$lecture_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership through course
$query = "SELECT l.id, l.course_id FROM lectures l JOIN courses c ON l.course_id = c.id WHERE l.id = '$lecture_id' AND c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$lecture = mysqli_fetch_assoc($result);

if ($lecture) {
    $query = "DELETE FROM lectures WHERE id = '$lecture_id'";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', 'Lecture deleted successfully.');
    } else {
        set_flash_message('error', 'Failed to delete lecture.');
    }
    redirect('manage_course.php?id=' . $lecture['course_id']);
} else {
    set_flash_message('error', 'Lecture not found or permission denied.');
    redirect('courses.php');
}
?>
