<?php
// instructor/add_assignment.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$instructor_id = $_SESSION['user_id'];
$error = '';

// Get instructor's courses
$query = "SELECT id, title FROM courses WHERE instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$courses = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $courses[] = $row;
    }
}

if (isset($_POST['submit'])) {
    $course_id = (int)$_POST['course_id'];
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $due_date = sanitize_input($_POST['due_date']);
    $attachment_path = null;

    // Verify course belongs to instructor
    $valid_course = false;
    foreach ($courses as $c) {
        if ($c['id'] == $course_id) {
            $valid_course = true;
            break;
        }
    }

    if (!$valid_course) {
        $error = 'Invalid course selected.';
    } elseif (empty($title) || empty($description) || empty($due_date)) {
        $error = 'All fields are required.';
    } else {
        // Handle optional attachment upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['attachment']['tmp_name'];
            $name = basename($_FILES['attachment']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed_types = ['pdf', 'doc', 'docx', 'zip'];

            if (in_array($ext, $allowed_types)) {
                $new_name = uniqid('assign_') . '.' . $ext;
                $destination = '../uploads/assignments/' . $new_name;
                if (move_uploaded_file($tmp_name, $destination)) {
                    $attachment_path = 'uploads/assignments/' . $new_name;
                } else {
                    $error = 'Failed to upload attachment.';
                }
            } else {
                $error = 'Invalid attachment format. Only PDF, DOC, DOCX, ZIP are allowed.';
            }
        }

        if (empty($error)) {
            $query = "INSERT INTO assignments (course_id, title, description, due_date, attachment_path) VALUES ('$course_id', '$title', '$description', '$due_date', '$attachment_path')";
            if (mysqli_query($conn, $query)) {
                set_flash_message('success', 'Assignment created successfully.');
                redirect('assignments.php');
            } else {
                $error = 'Failed to create assignment.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Add Assignment</h2>
            <a href="assignments.php" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_assignment.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="course_id">Course *</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c) { ?>
                            <option value="<?= $c['id'] ?>" <?= (isset($course_id) && $course_id == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['title']) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="3" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" required value="<?= isset($due_date) ? htmlspecialchars($due_date) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="attachment">Attachment (Optional PDF, DOC, ZIP)</label>
                    <input type="file" id="attachment" name="attachment">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Create Assignment</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
