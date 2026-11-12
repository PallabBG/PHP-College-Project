<?php
// student/course.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];
global $conn;

// Verify enrollment
$query = "SELECT id FROM enrollments WHERE student_id = '$student_id' AND course_id = '$course_id'";
$result = mysqli_query($conn, $query);
if (!mysqli_fetch_row($result)) {
    set_flash_message('error', 'You must enroll in this course first.');
    redirect('courses.php');
}

// Get course info
$query = "SELECT * FROM courses WHERE id = '$course_id'";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);

// Get lectures
$query = "SELECT l.*, IFNULL(lp.watched, 0) as watched FROM lectures l LEFT JOIN lecture_progress lp ON l.id = lp.lecture_id AND lp.student_id = '$student_id' WHERE l.course_id = '$course_id' ORDER BY l.order_number, l.created_at";
$result = mysqli_query($conn, $query);
$lectures = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lectures[] = $row;
    }
}

// Get materials
$query = "SELECT * FROM materials WHERE course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$materials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $materials[] = $row;
    }
}

// Get quizzes
$query = "SELECT q.*, (SELECT score FROM quiz_attempts WHERE quiz_id = q.id AND student_id = '$student_id' ORDER BY attempted_at DESC LIMIT 1) as best_score FROM quizzes q WHERE q.course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$quizzes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
    }
}

// Get assignments
$query = "SELECT a.*, (SELECT status FROM assignment_submissions WHERE assignment_id = a.id AND student_id = '$student_id' LIMIT 1) as submission_status FROM assignments a WHERE a.course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$assignments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $assignments[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2><?= htmlspecialchars($course['title']) ?></h2>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left Column: Lectures -->
            <div>
                <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem;">
                    <h3>Course Lectures</h3>
                    <br>
                    <?php if (count($lectures) > 0) { ?>
                        <div class="lecture-list">
                            <?php foreach ($lectures as $lec) { ?>
                                <div class="lecture-item">
                                    <div>
                                        <strong><?= htmlspecialchars($lec['order_number']) ?>. <?= htmlspecialchars($lec['title']) ?></strong>
                                        <p style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($lec['duration'] ?? '') ?></p>
                                    </div>
                                    <div>
                                        <?php if ($lec['watched']) { ?>
                                            <span style="color: var(--success); margin-right: 1rem;">&#10003; Completed</span>
                                        <?php } ?>
                                        <a href="lecture.php?id=<?= $lec['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem;">Watch</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p>No lectures available for this course yet.</p>
                    <?php } ?>
                </div>
            </div>

            <!-- Right Column: Materials, Quizzes, Assignments -->
            <div>
                <!-- Materials -->
                <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
                    <h3>Materials</h3>
                    <br>
                    <?php if (count($materials) > 0) { ?>
                        <ul style="list-style: none;">
                            <?php foreach ($materials as $mat) { ?>
                                <li style="margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                                    <span><?= htmlspecialchars($mat['title']) ?> (<?= htmlspecialchars($mat['file_type']) ?>)</span>
                                    <a href="<?= BASE_URL ?>/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Download</a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">No materials available.</p>
                    <?php } ?>
                </div>

                <!-- Quizzes -->
                <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
                    <h3>Quizzes</h3>
                    <br>
                    <?php if (count($quizzes) > 0) { ?>
                        <ul style="list-style: none;">
                            <?php foreach ($quizzes as $quiz) { ?>
                                <li style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                    <span><?= htmlspecialchars($quiz['title']) ?></span>
                                    <div>
                                        <?php if ($quiz['best_score'] !== null) { ?>
                                            <span style="font-size: 0.8rem; color: var(--success); margin-right: 0.5rem;">Score: <?= $quiz['best_score'] ?>/<?= $quiz['total_marks'] ?></span>
                                        <?php } ?>
                                        <a href="quiz.php?id=<?= $quiz['id'] ?>" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Take Quiz</a>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">No quizzes available.</p>
                    <?php } ?>
                </div>

                <!-- Assignments -->
                <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
                    <h3>Assignments</h3>
                    <br>
                    <?php if (count($assignments) > 0) { ?>
                        <ul style="list-style: none;">
                            <?php foreach ($assignments as $assign) { ?>
                                <li style="margin-bottom: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between;">
                                        <strong><?= htmlspecialchars($assign['title']) ?></strong>
                                        <a href="assignment.php?id=<?= $assign['id'] ?>" class="btn btn-primary" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">View</a>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Due: <?= htmlspecialchars($assign['due_date']) ?></div>
                                    <?php if ($assign['submission_status']) { ?>
                                        <div style="font-size: 0.8rem; color: var(--success);">Status: <?= ucfirst($assign['submission_status']) ?></div>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } else { ?>
                        <p style="font-size: 0.875rem; color: var(--text-muted);">No assignments available.</p>
                    <?php } ?>
                </div>

            </div>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
