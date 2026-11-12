<?php
// instructor/manage_course.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    redirect('courses.php');
}

$course_id = (int)$_GET['id'];
$instructor_id = $_SESSION['user_id'];
global $conn;

// Verify ownership
$query = "SELECT * FROM courses WHERE id = '$course_id' AND instructor_id = '$instructor_id'";
$result = mysqli_query($conn, $query);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    set_flash_message('error', 'Course not found or permission denied.');
    redirect('courses.php');
}

// Fetch Lectures
$query = "SELECT * FROM lectures WHERE course_id = '$course_id' ORDER BY order_number, created_at";
$result = mysqli_query($conn, $query);
$lectures = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lectures[] = $row;
    }
}

// Fetch Materials
$query = "SELECT * FROM materials WHERE course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$materials = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $materials[] = $row;
    }
}

// Fetch Quizzes
$query = "SELECT * FROM quizzes WHERE course_id = '$course_id'";
$result = mysqli_query($conn, $query);
$quizzes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $quizzes[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Manage Course: <?= htmlspecialchars($course['title']) ?></h2>
            <a href="courses.php" class="btn btn-primary">Back to Courses</a>
        </div>

        <!-- Lectures Section -->
        <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Lectures</h3>
                <a href="add_lecture.php?course_id=<?= $course_id ?>" class="btn btn-primary">+ Add Lecture</a>
            </div>
            <?php if (count($lectures) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lectures as $lec) { ?>
                            <tr>
                                <td><?= htmlspecialchars($lec['order_number']) ?></td>
                                <td><?= htmlspecialchars($lec['title']) ?></td>
                                <td><?= htmlspecialchars($lec['duration'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="delete_lecture.php?id=<?= $lec['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this lecture?');" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No lectures added yet.</p>
            <?php } ?>
        </div>

        <!-- Materials Section -->
        <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Course Materials</h3>
                <a href="add_material.php?course_id=<?= $course_id ?>" class="btn btn-primary">+ Add Material</a>
            </div>
            <?php if (count($materials) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materials as $mat) { ?>
                            <tr>
                                <td><?= htmlspecialchars($mat['title']) ?></td>
                                <td><?= htmlspecialchars($mat['file_type']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">View</a>
                                    <a href="delete_material.php?id=<?= $mat['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this material?');" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No materials added yet.</p>
            <?php } ?>
        </div>

        <!-- Quizzes Section -->
        <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3>Quizzes</h3>
                <a href="add_quiz.php?course_id=<?= $course_id ?>" class="btn btn-primary">+ Add Quiz</a>
            </div>
            <?php if (count($quizzes) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Total Marks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $quiz) { ?>
                            <tr>
                                <td><?= htmlspecialchars($quiz['title']) ?></td>
                                <td><?= htmlspecialchars($quiz['total_marks']) ?></td>
                                <td>
                                    <a href="manage_quiz.php?id=<?= $quiz['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Manage Questions</a>
                                    <a href="delete_quiz.php?id=<?= $quiz['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this quiz?');" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No quizzes added yet.</p>
            <?php } ?>
        </div>

    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
