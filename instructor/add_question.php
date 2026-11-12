<?php
// instructor/add_question.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['quiz_id'])) {
    redirect('courses.php');
}

$quiz_id = (int)$_GET['quiz_id'];
$instructor_id = $_SESSION['user_id'];
global $conn;
$error = '';

// Verify ownership
$query = "SELECT q.id, q.title FROM quizzes q JOIN courses c ON q.course_id = c.id WHERE q.id = '$quiz_id' AND c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($result);

if (!$quiz) {
    set_flash_message('error', 'Quiz not found or permission denied.');
    redirect('courses.php');
}

if (isset($_POST['submit'])) {
    $question = sanitize_input($_POST['question']);
    $option_a = sanitize_input($_POST['option_a']);
    $option_b = sanitize_input($_POST['option_b']);
    $option_c = sanitize_input($_POST['option_c']);
    $option_d = sanitize_input($_POST['option_d']);
    $correct_answer = sanitize_input($_POST['correct_answer']);
    $marks = (int)$_POST['marks'];

    if (empty($question) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d)) {
        $error = 'All fields are required.';
    } elseif (!in_array($correct_answer, ['A', 'B', 'C', 'D'])) {
        $error = 'Invalid correct answer selection.';
    } else {
        $query = "INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer, marks) VALUES ('$quiz_id', '$question', '$option_a', '$option_b', '$option_c', '$option_d', '$correct_answer', '$marks')";
        if (mysqli_query($conn, $query)) {
            set_flash_message('success', 'Question added successfully.');
            redirect('manage_quiz.php?id=' . $quiz_id);
        } else {
            $error = 'Failed to add question.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Add Question to: <?= htmlspecialchars($quiz['title']) ?></h2>
            <a href="manage_quiz.php?id=<?= $quiz_id ?>" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 800px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_question.php?quiz_id=<?= $quiz_id ?>">
                <div class="form-group">
                    <label for="question">Question Text *</label>
                    <textarea id="question" name="question" rows="3" required><?= isset($question) ? htmlspecialchars($question) : '' ?></textarea>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="option_a">Option A *</label>
                        <input type="text" id="option_a" name="option_a" required value="<?= isset($option_a) ? htmlspecialchars($option_a) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="option_b">Option B *</label>
                        <input type="text" id="option_b" name="option_b" required value="<?= isset($option_b) ? htmlspecialchars($option_b) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="option_c">Option C *</label>
                        <input type="text" id="option_c" name="option_c" required value="<?= isset($option_c) ? htmlspecialchars($option_c) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="option_d">Option D *</label>
                        <input type="text" id="option_d" name="option_d" required value="<?= isset($option_d) ? htmlspecialchars($option_d) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="correct_answer">Correct Answer *</label>
                    <select id="correct_answer" name="correct_answer" required>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="marks">Marks *</label>
                    <input type="number" id="marks" name="marks" value="1" min="1" required>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Add Question</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
