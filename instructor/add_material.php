<?php
// instructor/add_material.php
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
    } elseif (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload is required.';
    } else {
        $tmp_name = $_FILES['material_file']['tmp_name'];
        $name = basename($_FILES['material_file']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'];

        if (in_array($ext, $allowed_types)) {
            $new_name = uniqid('mat_') . '.' . $ext;
            $destination = '../uploads/documents/' . $new_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                $file_path = 'uploads/documents/' . $new_name;
                $file_type = $ext;
                
                $query = "INSERT INTO materials (course_id, title, description, file_path, file_type) VALUES ('$course_id', '$title', '$description', '$file_path', '$file_type')";
                if (mysqli_query($conn, $query)) {
                    set_flash_message('success', 'Material added successfully.');
                    redirect('manage_course.php?id=' . $course_id);
                } else {
                    $error = 'Failed to save material.';
                }
            } else {
                $error = 'Failed to move uploaded file.';
            }
        } else {
            $error = 'Invalid file format. Allowed types: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Add Material to: <?= htmlspecialchars($course['title']) ?></h2>
            <a href="manage_course.php?id=<?= $course_id ?>" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_material.php?course_id=<?= $course_id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Material Title *</label>
                    <input type="text" id="title" name="title" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="material_file">Document File (PDF, DOC, DOCX, PPT, TXT, ZIP) *</label>
                    <input type="file" id="material_file" name="material_file" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Add Material</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
