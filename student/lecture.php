<?php
// student/lecture.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$lecture_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];
global $conn;

// Get lecture and course info
$query = "SELECT l.*, c.title as course_title, c.id as course_id FROM lectures l JOIN courses c ON l.course_id = c.id WHERE l.id = '$lecture_id'";
$result = mysqli_query($conn, $query);
$lecture = mysqli_fetch_assoc($result);

if (!$lecture) {
    set_flash_message('error', 'Lecture not found.');
    redirect('courses.php');
}

// Verify enrollment
$query = "SELECT id FROM enrollments WHERE student_id = '$student_id' AND course_id = '{$lecture['course_id']}'";
$result = mysqli_query($conn, $query);
if (!mysqli_fetch_assoc($result)) {
    set_flash_message('error', 'You must enroll in this course to view lectures.');
    redirect('courses.php');
}

// Mark as watched logic (progress)
if (isset($_POST['mark_watched'])) {
    $query = "INSERT IGNORE INTO lecture_progress (student_id, lecture_id) VALUES ('$student_id', '$lecture_id')";
    mysqli_query($conn, $query);

    // Update overall course progress
    // 1. Get total lectures
    $query = "SELECT COUNT(*) FROM lectures WHERE course_id = '{$lecture['course_id']}'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_row($result);
    $total_lectures = $row[0];

    if ($total_lectures > 0) {
        // 2. Get completed lectures for this student
        $query = "SELECT COUNT(*) FROM lecture_progress lp JOIN lectures l ON lp.lecture_id = l.id WHERE lp.student_id = '$student_id' AND l.course_id = '{$lecture['course_id']}'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        $completed_lectures = $row[0];

        // 3. Calculate percentage
        $percentage = ($completed_lectures / $total_lectures) * 100;

        // 4. Update course_progress
        $query = "UPDATE course_progress SET progress_percentage = '$percentage' WHERE student_id = '$student_id' AND course_id = '{$lecture['course_id']}'";
        mysqli_query($conn, $query);
    }

    set_flash_message('success', 'Marked as completed!');
    redirect('course.php?id=' . $lecture['course_id']);
}

// Check if already watched
$query = "SELECT id FROM lecture_progress WHERE student_id = '$student_id' AND lecture_id = '$lecture_id'";
$result = mysqli_query($conn, $query);
$is_watched = mysqli_fetch_assoc($result) ? true : false;

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2><?= htmlspecialchars($lecture['course_title']) ?> - <?= htmlspecialchars($lecture['title']) ?></h2>
            <a href="course.php?id=<?= $lecture['course_id'] ?>" class="btn btn-primary">Back to Course</a>
        </div>

        <div class="auth-card" style="max-width: 100%; padding: 0; overflow: hidden; margin-bottom: 2rem;">
            <div class="video-container" style="background: #000; padding: 1rem;">
                <video controls style="width: 100%; max-height: 60vh;">
                    <source src="<?= BASE_URL ?>/<?= htmlspecialchars($lecture['video_path']) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div style="padding: 2rem;">
                <h3><?= htmlspecialchars($lecture['title']) ?></h3>
                <p style="margin: 1rem 0; color: var(--text-muted);"><?= nl2br(htmlspecialchars($lecture['description'])) ?></p>
                
                <?php if (!$is_watched) { ?>
                    <form method="POST" style="margin-top: 2rem;">
                        <input type="hidden" name="mark_watched" value="1">
                        <button type="submit" class="btn btn-primary">Mark as Completed</button>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-success" style="display: inline-block; margin-top: 2rem;">
                        &#10003; You have completed this lecture.
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
