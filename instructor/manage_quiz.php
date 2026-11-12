<?php
// instructor/manage_quiz.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$quiz_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership
$query = "SELECT q.*, c.title as course_title, c.instructor_id FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = '$quiz_id'";
$result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($result);

if (!$quiz || $quiz['instructor_id'] != $instructor_id) {
    set_flash_message('error', 'Quiz not found or permission denied.');
    redirect('courses.php');
}

// Get Questions
$query = "SELECT * FROM quiz_questions WHERE quiz_id = '$quiz_id'";
$result = mysqli_query($conn, $query);
$questions = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $questions[] = $row;
    }
}

// Update total marks just in case
$total_marks = array_sum(array_column($questions, 'marks'));
$query = "UPDATE quizzes SET total_marks = '$total_marks' WHERE id = '$quiz_id'";
mysqli_query($conn, $query);
$quiz['total_marks'] = $total_marks;

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Manage Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>
            <a href="manage_course.php?id=<?= $quiz['course_id'] ?>" class="btn btn-primary">Back to Course</a>
        </div>

        <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Questions (Total Marks: <?= $quiz['total_marks'] ?>)</h3>
                <a href="add_question.php?quiz_id=<?= $quiz_id ?>" class="btn btn-primary">+ Add Question</a>
            </div>
            
            <?php if (count($questions) > 0) { ?>
                <?php foreach ($questions as $index => $q) { ?>
                    <div style="border: 1px solid var(--border-color); padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem;">
                        <h4>Q<?= $index + 1 ?>: <?= htmlspecialchars($q['question']) ?> (<?= $q['marks'] ?> marks)</h4>
                        <ul style="list-style: none; padding-left: 1rem; margin: 0.5rem 0;">
                            <li style="<?= $q['correct_answer'] === 'A' ? 'font-weight: bold; color: var(--success);' : '' ?>">A) <?= htmlspecialchars($q['option_a']) ?></li>
                            <li style="<?= $q['correct_answer'] === 'B' ? 'font-weight: bold; color: var(--success);' : '' ?>">B) <?= htmlspecialchars($q['option_b']) ?></li>
                            <li style="<?= $q['correct_answer'] === 'C' ? 'font-weight: bold; color: var(--success);' : '' ?>">C) <?= htmlspecialchars($q['option_c']) ?></li>
                            <li style="<?= $q['correct_answer'] === 'D' ? 'font-weight: bold; color: var(--success);' : '' ?>">D) <?= htmlspecialchars($q['option_d']) ?></li>
                        </ul>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No questions added to this quiz yet.</p>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
