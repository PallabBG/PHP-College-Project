<?php
// instructor/edit_course.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;
$error = '';

// Verify ownership and get course data
$query = "SELECT * FROM courses WHERE id = '$course_id' AND instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    set_flash_message('error', 'Course not found or permission denied.');
    redirect('courses.php');
}

if (isset($_POST['submit'])) {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $category = sanitize_input($_POST['category']);
    $thumbnail_path = $course['thumbnail'];

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
            $query = "UPDATE courses SET title = '$title', description = '$description', category = '$category', thumbnail = '$thumbnail_path' WHERE id = '$course_id' AND instructor_id = '$instructor_id'";
            if (mysqli_query($conn, $query)) {
                set_flash_message('success', 'Course updated successfully.');
                redirect('courses.php');
            } else {
                $error = 'Failed to update course.';
            }
        }
    }
} else {
    $title = $course['title'];
    $description = $course['description'];
    $category = $course['category'];
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Edit Course: <?= htmlspecialchars($title) ?></h2>
            <a href="courses.php" class="btn btn-primary">Back to Courses</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="edit_course.php?id=<?= $course_id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Course Title *</label>
                    <input type="text" id="title" name="title" required value="<?= htmlspecialchars($title) ?>">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" value="<?= htmlspecialchars($category) ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="thumbnail">Update Thumbnail (Image)</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/png, image/jpeg, image/webp">
                    <?php if ($course['thumbnail']) { ?>
                        <div style="margin-top:10px;">
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($course['thumbnail']) ?>" alt="Current Thumbnail" style="max-width: 200px;">
                        </div>
                    <?php } ?>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Update Course</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
