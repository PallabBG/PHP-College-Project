<?php
// instructor/add_quiz.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['course_id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['course_id'];
$instructor_id = $_SESSION['user_id'];
global $conn;
$error = '';

// Verify ownership
$query = "SELECT id, title FROM courses WHERE id = '$course_id' AND instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    set_flash_message('error', 'Course not found or permission denied.');
    redirect('courses.php');
}

if (isset($_POST['submit'])) {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);

    if (empty($title)) {
        $error = 'Title is required.';
    } else {
        $query = "INSERT INTO quizzes (course_id, title, description) VALUES ('$course_id', '$title', '$description')";
        if (mysqli_query($conn, $query)) {
            set_flash_message('success', 'Quiz added successfully. Now add some questions.');
            $quiz_id = mysqli_insert_id($conn);
            redirect('manage_quiz.php?id=' . $quiz_id);
        } else {
            $error = 'Failed to create quiz.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Add Quiz to: <?= htmlspecialchars($course['title']) ?></h2>
            <a href="manage_course.php?id=<?= $course_id ?>" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_quiz.php?course_id=<?= $course_id ?>">
                <div class="form-group">
                    <label for="title">Quiz Title *</label>
                    <input type="text" id="title" name="title" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Create Quiz</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
