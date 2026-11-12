<?php
// instructor/delete_quiz.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$quiz_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership through course
$query = "SELECT q.id, q.course_id FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = '$quiz_id' AND c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($result);

if ($quiz) {
    $query = "DELETE FROM quizzes WHERE id = '$quiz_id'";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', 'Quiz deleted successfully.');
    } else {
        set_flash_message('error', 'Failed to delete quiz.');
    }
    redirect('manage_course.php?id=' . $quiz['course_id']);
} else {
    set_flash_message('error', 'Quiz not found or permission denied.');
    redirect('courses.php');
}
?>
