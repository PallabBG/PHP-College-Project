<?php
// instructor/dashboard.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$instructor_id = $_SESSION['user_id'];

// Get stats
$query = "SELECT COUNT(*) FROM courses WHERE instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$total_courses = $row[0];

$query = "SELECT COUNT(DISTINCT e.student_id) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$total_students = $row[0];

$query = "SELECT COUNT(*) FROM lectures l JOIN courses c ON l.course_id = c.id WHERE c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$total_lectures = $row[0];

$query = "SELECT COUNT(*) FROM assignments a JOIN courses c ON a.course_id = c.id WHERE c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$total_assignments = $row[0];

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Instructor Dashboard</h2>
            <a href="add_course.php" class="btn btn-primary">+ Create New Course</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Courses</div>
                <div class="stat-value"><?= $total_courses ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Students Enrolled</div>
                <div class="stat-value"><?= $total_students ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Lectures</div>
                <div class="stat-value"><?= $total_lectures ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Assignments</div>
                <div class="stat-value"><?= $total_assignments ?></div>
            </div>
        </div>

        <h3>Recent Activity</h3>
        <!-- You could add recent student enrollments or quiz submissions here -->
        <p>Welcome to your instructor dashboard. Use the sidebar to manage your courses, view student progress, and grade assignments.</p>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
