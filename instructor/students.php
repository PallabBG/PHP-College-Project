<?php
// instructor/students.php
$required_role = 'instructor';
require_once '../includes/role_check.php';
require_once '../config/database.php';

global $conn;
$instructor_id = $_SESSION['user_id'];

// Get students enrolled in instructor's courses with their progress
$query = "
    SELECT 
        u.name as student_name, 
        u.email as student_email,
        c.title as course_title,
        e.enrolled_at,
        IFNULL(cp.progress_percentage, 0) as progress
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN course_progress cp ON cp.student_id = u.id AND cp.course_id = c.id
    WHERE c.instructor_id = '$instructor_id'
    ORDER BY e.enrolled_at DESC
";
$result = mysqli_query($conn, $query);
$students = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

require_once '../includes/header.php';
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <div class="dashboard-header">
            <h2>Student Progress</h2>
        </div>

        <div class="auth-card" style="max-width: 100%; padding: 1.5rem;">
            <?php if (count($students) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Enrolled Date</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s) { ?>
                            <tr>
                                <td><?= htmlspecialchars($s['student_name']) ?></td>
                                <td><?= htmlspecialchars($s['student_email']) ?></td>
                                <td><?= htmlspecialchars($s['course_title']) ?></td>
                                <td><?= format_date($s['enrolled_at']) ?></td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-bar" style="width: <?= $s['progress'] ?>%"></div>
                                    </div>
                                    <span style="font-size: 0.8rem;"><?= $s['progress'] ?>%</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No students enrolled in your courses yet.</p>
            <?php } ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>
