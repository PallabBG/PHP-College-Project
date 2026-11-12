<?php
// student/quiz.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$quiz_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];
global $conn;

// Get Quiz Info
$query = "SELECT * FROM quizzes WHERE id = '$quiz_id'";
$result = mysqli_query($conn, $query);
$quiz = mysqli_fetch_assoc($result);

if (!$quiz) {
    set_flash_message('error', 'Quiz not found.');
    redirect('dashboard.php');
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

if (isset($_POST['submit'])) {
    $score = 0;
    
    foreach ($questions as $q) {
        $q_id = $q['id'];
        if (isset($_POST['question_' . $q_id])) {
            $user_answer = $_POST['question_' . $q_id];
            if ($user_answer === $q['correct_answer']) {
                $score += $q['marks'];
            }
        }
    }

    // Save attempt
    $query = "INSERT INTO quiz_attempts (quiz_id, student_id, score, total_marks) VALUES ('$quiz_id', '$student_id', '$score', '{$quiz['total_marks']}')";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', "Quiz submitted! You scored $score out of {$quiz['total_marks']}.");
        redirect('course.php?id=' . $quiz['course_id']);
    } else {
        $error = "Failed to submit quiz.";
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Quiz: <?= htmlspecialchars($quiz['title']) ?></h2>
            <a href="course.php?id=<?= $quiz['course_id'] ?>" class="btn btn-primary">Back to Course</a>
        </div>

        <div class="auth-card" style="max-width: 800px; margin: 0; padding: 2rem;">
            <?php if (isset($error)) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            
            <p style="margin-bottom: 2rem; color: var(--text-muted);"><?= nl2br(htmlspecialchars($quiz['description'])) ?></p>

            <form method="POST" action="quiz.php?id=<?= $quiz_id ?>">
                <?php foreach ($questions as $index => $q) { ?>
                    <div style="margin-bottom: 2rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <h4 style="margin-bottom: 1rem;">Q<?= $index + 1 ?>: <?= htmlspecialchars($q['question']) ?> <span style="color: var(--text-muted); font-weight: normal;">(<?= $q['marks'] ?> marks)</span></h4>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <label>
                                <input type="radio" name="question_<?= $q['id'] ?>" value="A" required> 
                                <?= htmlspecialchars($q['option_a']) ?>
                            </label>
                            <label>
                                <input type="radio" name="question_<?= $q['id'] ?>" value="B" required> 
                                <?= htmlspecialchars($q['option_b']) ?>
                            </label>
                            <label>
                                <input type="radio" name="question_<?= $q['id'] ?>" value="C" required> 
                                <?= htmlspecialchars($q['option_c']) ?>
                            </label>
                            <label>
                                <input type="radio" name="question_<?= $q['id'] ?>" value="D" required> 
                                <?= htmlspecialchars($q['option_d']) ?>
                            </label>
                        </div>
                    </div>
                <?php } ?>

                <?php if (count($questions) > 0) { ?>
                    <button type="submit" name="submit" class="btn btn-primary">Submit Quiz</button>
                <?php } else { ?>
                    <p>No questions available for this quiz yet.</p>
                <?php } ?>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
