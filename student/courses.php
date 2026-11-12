<?php
// student/courses.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$student_id = $_SESSION['user_id'];

// Get all courses with instructor name
$query = "
    SELECT c.*, u.name as instructor_name,
    (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND student_id = '$student_id') as is_enrolled
    FROM courses c
    JOIN users u ON c.instructor_id = u.id
    ORDER BY c.created_at DESC
";
$result = mysqli_query($conn, $query);
$courses = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Browse Courses</h2>
        </div>

        <div class="courses-grid">
            <?php foreach ($courses as $course) { ?>
                <div class="course-card">
                    <?php if ($course['thumbnail']) { ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($course['thumbnail']) ?>" alt="Thumbnail" class="course-thumbnail">
                    <?php } else { ?>
                        <div class="course-thumbnail"></div>
                    <?php } ?>
                    <div class="course-content">
                        <div class="course-category"><?= htmlspecialchars($course['category'] ?? 'General') ?></div>
                        <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Instructor: <?= htmlspecialchars($course['instructor_name']) ?></p>
                        <p class="course-desc"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                        
                        <?php if ($course['is_enrolled'] > 0) { ?>
                            <a href="course.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-block" style="background-color: var(--success);">Already Enrolled - View Course</a>
                        <?php } else { ?>
                            <a href="enroll.php?id=<?= $course['id'] ?>" class="btn btn-primary btn-block">Enroll Now</a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
