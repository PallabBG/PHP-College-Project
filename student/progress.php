<?php
// student/progress.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$student_id = $_SESSION['user_id'];

// Get overall progress
$query = "
    SELECT c.title as course_title, c.id as course_id, IFNULL(cp.progress_percentage, 0) as progress
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN course_progress cp ON cp.course_id = c.id AND cp.student_id = '$student_id'
    WHERE e.student_id = '$student_id'
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
            <h2>My Progress</h2>
        </div>

        <div class="auth-card" style="max-width: 100%; padding: 1.5rem;">
            <?php if (count($courses) > 0) { ?>
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <?php foreach ($courses as $c) { ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <strong><?= htmlspecialchars($c['course_title']) ?></strong>
                                <span><?= $c['progress'] ?>%</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar" style="width: <?= $c['progress'] ?>%"></div>
                            </div>
                            <a href="course.php?id=<?= $c['course_id'] ?>" style="font-size: 0.8rem; margin-top: 0.5rem; display: inline-block;">Go to Course &rarr;</a>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>You haven't enrolled in any courses yet.</p>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
