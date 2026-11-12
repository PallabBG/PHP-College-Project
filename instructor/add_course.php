<?php
// instructor/add_course.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

$error = '';

if (isset($_POST['submit'])) {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $category = sanitize_input($_POST['category']);
    $instructor_id = $_SESSION['user_id'];
    $thumbnail_path = null;

    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        // Handle thumbnail upload
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['thumbnail']['tmp_name'];
            $name = basename($_FILES['thumbnail']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($ext, $allowed_types)) {
                $new_name = uniqid('thumb_') . '.' . $ext;
                $destination = '../uploads/thumbnails/' . $new_name;
                if (move_uploaded_file($tmp_name, $destination)) {
                    $thumbnail_path = 'uploads/thumbnails/' . $new_name;
                } else {
                    $error = 'Failed to upload thumbnail.';
                }
            } else {
                $error = 'Invalid thumbnail format. Only JPG, PNG, WEBP are allowed.';
            }
        }

        if (empty($error)) {
            global $conn;
            $query = "INSERT INTO courses (instructor_id, title, description, category, thumbnail) VALUES ('$instructor_id', '$title', '$description', '$category', '$thumbnail_path')";
            if (mysqli_query($conn, $query)) {
                set_flash_message('success', 'Course created successfully.');
                redirect('courses.php');
            } else {
                $error = 'Failed to create course.';
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
            <h2>Add New Course</h2>
            <a href="courses.php" class="btn btn-primary">Back to Courses</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_course.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= isset($category) ? htmlspecialchars($category) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="thumbnail">Course Thumbnail (Image)</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/png, image/jpeg, image/webp">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Create Course</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
