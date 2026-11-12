<?php
// student/dashboard.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$student_id = $_SESSION['user_id'];

// Get enrolled courses with progress
$query = "
    SELECT c.*, e.status as enrollment_status, cp.progress_percentage
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN course_progress cp ON cp.course_id = c.id AND cp.student_id = '$student_id'
    WHERE e.student_id = '$student_id'
";
$result = mysqli_query($conn, $query);
$enrolled_courses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $enrolled_courses[] = $row;
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Student Dashboard</h2>
            <a href="courses.php" class="btn btn-primary">Browse More Courses</a>
        </div>

        <h3>My Enrolled Courses</h3>
        <br>
        <?php if (count($enrolled_courses) > 0) { ?>
            <div class="courses-grid">
                <?php foreach ($enrolled_courses as $course) { ?>
                    <div class="course-card">
                        <?php if ($course['thumbnail']) { ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($course['thumbnail']) ?>" alt="Thumbnail" class="course-thumbnail">
                        <?php } else { ?>
                            <div class="course-thumbnail"></div>
                        <?php } ?>
                        <div class="course-content">
                            <div class="course-category"><?= htmlspecialchars($course['category'] ?? 'General') ?></div>
                            <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                            
                            <?php $progress = $course['progress_percentage'] ?? 0; ?>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?= $progress ?>%"></div>
                            </div>
                            <div style="font-size: 0.8rem; margin-bottom: 1rem; color: var(--text-muted);">
                                Progress: <?= $progress ?>%
                            </div>
                            
                            <a href="course.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-block">Continue Learning</a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="auth-card" style="max-width: 100%; text-align: center; padding: 3rem;">
                <p style="margin-bottom: 1.5rem; color: var(--text-muted);">You are not enrolled in any courses yet.</p>
                <a href="courses.php" class="btn btn-primary">Browse Courses</a>
            </div>
        <?php } ?>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
