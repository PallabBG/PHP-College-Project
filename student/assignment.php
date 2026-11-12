<?php
// student/assignment.php
$required_role = 'student';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$assignment_id = (int)$_GET['id'];
$student_id = $_SESSION['user_id'];
global $conn;
$error = '';

// Get Assignment Info
$query = "SELECT a.*, c.title as course_title FROM assignments a JOIN courses c ON a.course_id = c.id WHERE a.id = '$assignment_id'";
$result = mysqli_query($conn, $query);
$assignment = mysqli_fetch_assoc($result);

if (!$assignment) {
    set_flash_message('error', 'Assignment not found.');
    redirect('dashboard.php');
}

// Check existing submission
$query = "SELECT * FROM assignment_submissions WHERE assignment_id = '$assignment_id' AND student_id = '$student_id'";
$result = mysqli_query($conn, $query);
$submission = mysqli_fetch_assoc($result);

if (isset($_POST['submit']) && !$submission) {
    if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload is required.';
    } else {
        $tmp_name = $_FILES['submission_file']['tmp_name'];
        $name = basename($_FILES['submission_file']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed_types = ['pdf', 'doc', 'docx', 'zip'];

        if (in_array($ext, $allowed_types)) {
            $new_name = uniqid('sub_') . '.' . $ext;
            $destination = '../uploads/assignments/' . $new_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                $submission_path = 'uploads/assignments/' . $new_name;
                
                $query = "INSERT INTO assignment_submissions (assignment_id, student_id, submission_path, status) VALUES ('$assignment_id', '$student_id', '$submission_path', 'submitted')";
                if (mysqli_query($conn, $query)) {
                    set_flash_message('success', 'Assignment submitted successfully.');
                    redirect('assignment.php?id=' . $assignment_id);
                } else {
                    $error = 'Failed to submit assignment.';
                }
            } else {
                $error = 'Failed to move uploaded file.';
            }
        } else {
            $error = 'Invalid file format. Only PDF, DOC, DOCX, ZIP are allowed.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Assignment: <?= htmlspecialchars($assignment['title']) ?></h2>
            <a href="course.php?id=<?= $assignment['course_id'] ?>" class="btn btn-primary">Back to Course</a>
        </div>

        <div class="auth-card" style="max-width: 800px; margin: 0;">
            <div style="margin-bottom: 2rem;">
                <p><strong>Due Date:</strong> <?= htmlspecialchars($assignment['due_date']) ?></p>
                <div style="margin-top: 1rem;">
                    <strong>Description:</strong>
                    <p style="margin-top: 0.5rem; color: var(--text-muted);"><?= nl2br(htmlspecialchars($assignment['description'])) ?></p>
                </div>
                <?php if ($assignment['attachment_path']) { ?>
                    <div style="margin-top: 1rem;">
                        <a href="<?= BASE_URL ?>/<?= htmlspecialchars($assignment['attachment_path']) ?>" target="_blank" class="btn btn-primary">Download Attachment</a>
                    </div>
                <?php } ?>
            </div>

            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">

            <h3>Your Submission</h3>
            <br>
            <?php if ($submission) { ?>
                <div class="alert alert-success">You have already submitted this assignment.</div>
                <p><strong>Submitted At:</strong> <?= format_date($submission['submitted_at']) ?></p>
                <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($submission['status'])) ?></p>
                <?php if ($submission['marks'] !== null) { ?>
                    <p><strong>Marks:</strong> <?= htmlspecialchars($submission['marks']) ?></p>
                    <p><strong>Feedback:</strong> <?= nl2br(htmlspecialchars($submission['feedback'])) ?></p>
                <?php } ?>
                <div style="margin-top: 1rem;">
                    <a href="<?= BASE_URL ?>/<?= htmlspecialchars($submission['submission_path']) ?>" target="_blank" class="btn btn-primary">View My Submission</a>
                </div>
            <?php } else { ?>
                <?php if ($error) { ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <form method="POST" action="assignment.php?id=<?= $assignment_id ?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="submission_file">Upload Submission (PDF, DOC, DOCX, ZIP) *</label>
                        <input type="file" id="submission_file" name="submission_file" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit Assignment</button>
                </form>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
