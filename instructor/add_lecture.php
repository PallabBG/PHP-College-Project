<?php
// instructor/add_lecture.php
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
    $duration = sanitize_input($_POST['duration']);
    $order_number = (int)$_POST['order_number'];

    if (empty($title)) {
        $error = 'Title is required.';
    } elseif (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Video upload is required.';
    } else {
        $tmp_name = $_FILES['video']['tmp_name'];
        $name = basename($_FILES['video']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $allowed_types = ['mp4', 'webm', 'ogg'];

        if (in_array($ext, $allowed_types)) {
            $new_name = uniqid('vid_') . '.' . $ext;
            $destination = '../uploads/videos/' . $new_name;
            if (move_uploaded_file($tmp_name, $destination)) {
                $video_path = 'uploads/videos/' . $new_name;
                
                $query = "INSERT INTO lectures (course_id, title, description, video_path, duration, order_number) VALUES ('$course_id', '$title', '$description', '$video_path', '$duration', '$order_number')";
                if (mysqli_query($conn, $query)) {
                    set_flash_message('success', 'Lecture added successfully.');
                    redirect('manage_course.php?id=' . $course_id);
                } else {
                    $error = 'Failed to save lecture.';
                }
            } else {
                $error = 'Failed to move uploaded video file.';
            }
        } else {
            $error = 'Invalid video format. Only MP4, WEBM, OGG are allowed.';
        }
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Add Lecture to: <?= htmlspecialchars($course['title']) ?></h2>
            <a href="manage_course.php?id=<?= $course_id ?>" class="btn btn-primary">Back</a>
        </div>

        <div class="auth-card" style="max-width: 600px; margin: 0;">
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="add_lecture.php?course_id=<?= $course_id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Lecture Title *</label>
                    <input type="text" id="title" name="title" required value="<?= isset($title) ? htmlspecialchars($title) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="duration">Duration (e.g. 10:30)</label>
                    <input type="text" id="duration" name="duration" value="<?= isset($duration) ? htmlspecialchars($duration) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="order_number">Order Number</label>
                    <input type="number" id="order_number" name="order_number" value="0">
                </div>
                <div class="form-group">
                    <label for="video">Video File (MP4, WEBM, OGG) *</label>
                    <input type="file" id="video" name="video" accept="video/mp4, video/webm, video/ogg" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Add Lecture</button>
            </form>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
