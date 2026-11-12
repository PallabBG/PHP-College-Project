<?php
// instructor/view_submissions.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('assignments.php');
}

$assignment_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership
$query = "SELECT a.*, c.title as course_title FROM assignments a JOIN courses c ON a.course_id = c.id WHERE a.id = '$assignment_id' AND c.instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$assignment = mysqli_fetch_assoc($result);

if (!$assignment) {
    set_flash_message('error', 'Assignment not found or permission denied.');
    redirect('assignments.php');
}

// Handle grading
if (isset($_POST['submission_id'])) {
    $submission_id = (int)$_POST['submission_id'];
    $marks = (int)$_POST['marks'];
    $feedback = sanitize_input($_POST['feedback']);

    $query = "UPDATE assignment_submissions SET marks = '$marks', feedback = '$feedback', status = 'graded' WHERE id = '$submission_id' AND assignment_id = '$assignment_id'";
    if (mysqli_query($conn, $query)) {
        set_flash_message('success', 'Submission graded successfully.');
    } else {
        set_flash_message('error', 'Failed to grade submission.');
    }
    // reload page to avoid POST resubmission
    redirect("view_submissions.php?id=$assignment_id");
}

// Get submissions
$query = "
    SELECT s.*, u.name as student_name, u.email as student_email 
    FROM assignment_submissions s 
    JOIN users u ON s.student_id = u.id 
    WHERE s.assignment_id = '$assignment_id' 
    ORDER BY s.submitted_at DESC
";
$result = mysqli_query($conn, $query);
$submissions = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $submissions[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Submissions for: <?= htmlspecialchars($assignment['title']) ?></h2>
            <a href="assignments.php" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 100%; padding: 1.5rem;">
            <?php if (count($submissions) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Submitted At</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub) { ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($sub['student_name']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($sub['student_email']) ?></small>
                                </td>
                                <td><?= format_date($sub['submitted_at']) ?></td>
                                <td><a href="<?= BASE_URL ?>/<?= htmlspecialchars($sub['submission_path']) ?>" target="_blank" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">View File</a></td>
                                <td><?= ucfirst(htmlspecialchars($sub['status'])) ?></td>
                                <td>
                                    <?php if ($sub['status'] === 'graded') { ?>
                                        <span>Marks: <?= htmlspecialchars($sub['marks']) ?></span>
                                    <?php } else { ?>
                                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                            <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                                            <input type="number" name="marks" placeholder="Marks" required style="width: 70px; padding: 0.25rem;">
                                            <input type="text" name="feedback" placeholder="Feedback (opt)" style="padding: 0.25rem;">
                                            <button type="submit" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Grade</button>
                                        </form>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No submissions yet.</p>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
