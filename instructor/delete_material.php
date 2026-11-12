<?php
// instructor/delete_material.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$material_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership through course
$query = "SELECT m.id, m.course_id FROM materials m JOIN courses c ON m.course_id = c.id WHERE m.id = '$material_id' AND c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$material = mysqli_fetch_assoc($result);

if ($material) {
    $query = "DELETE FROM materials WHERE id = '$material_id'";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', 'Material deleted successfully.');
    } else {
        set_flash_message('error', 'Failed to delete material.');
    }
    redirect('manage_course.php?id=' . $material['course_id']);
} else {
    set_flash_message('error', 'Material not found or permission denied.');
    redirect('courses.php');
}
?>
