<?php
// includes/sidebar.php
$role = get_user_role();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <ul class="sidebar-menu">
        <?php if ($role === 'student') { ?>
            <li><a href="<?= BASE_URL ?>/student/dashboard.php" class="sidebar-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/student/courses.php" class="sidebar-link <?= $current_page == 'courses.php' ? 'active' : '' ?>">Browse Courses</a></li>
            <li><a href="<?= BASE_URL ?>/student/progress.php" class="sidebar-link <?= $current_page == 'progress.php' ? 'active' : '' ?>">My Progress</a></li>
        <?php } elseif ($role === 'instructor') { ?>
            <li><a href="<?= BASE_URL ?>/instructor/dashboard.php" class="sidebar-link <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/instructor/courses.php" class="sidebar-link <?= $current_page == 'courses.php' ? 'active' : '' ?>">My Courses</a></li>
            <li><a href="<?= BASE_URL ?>/instructor/assignments.php" class="sidebar-link <?= $current_page == 'assignments.php' ? 'active' : '' ?>">Assignments</a></li>
            <li><a href="<?= BASE_URL ?>/instructor/students.php" class="sidebar-link <?= $current_page == 'students.php' ? 'active' : '' ?>">Student Progress</a></li>
        <?php } ?>
    </ul>
</aside>
