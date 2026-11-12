<?php
// instructor/assignments.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$instructor_id = $_SESSION['user_id'];

// Get all assignments for this instructor's courses
$query = "SELECT a.*, c.title as course_title FROM assignments a JOIN courses c ON a.course_id = c.id WHERE c.instructor_id = '$instructor_id' ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $query);
$assignments = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $assignments[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Manage Assignments</h2>
            <a href="add_assignment.php" class="btn btn-primary">+ Create Assignment</a>
        </div>

        <div class="auth-card" style="max-width: 100%; margin-bottom: 2rem; padding: 1.5rem;">
            <?php if (count($assignments) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Title</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $a) { ?>
                            <tr>
                                <td><?= htmlspecialchars($a['course_title']) ?></td>
                                <td><?= htmlspecialchars($a['title']) ?></td>
                                <td><?= htmlspecialchars($a['due_date']) ?></td>
                                <td>
                                    <!-- Here instructor can view submissions -->
                                    <a href="view_submissions.php?id=<?= $a['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">View Submissions</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No assignments created yet.</p>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
