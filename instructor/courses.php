<?php
// instructor/courses.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$instructor_id = $_SESSION['user_id'];

$query = "SELECT * FROM courses WHERE instructor_id = '$instructor_id' ORDER BY created_at DESC";
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
            <h2>My Courses</h2>
            <a href="add_course.php" class="btn btn-primary">+ Create New Course</a>
        </div>

        <?php if (count($courses) > 0) { ?>
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
                            <p class="course-desc"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                            <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <a href="edit_course.php?id=<?= $course['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Edit</a>
                                <a href="manage_course.php?id=<?= $course['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem; background-color: #3b82f6;">Manage Content</a>
                                <a href="delete_course.php?id=<?= $course['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this course? All lectures, quizzes, and progress will be lost.');" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>You haven't created any courses yet.</p>
        <?php } ?>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
