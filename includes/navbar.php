<?php
// includes/navbar.php
?>
<nav class="navbar">
    <div class="nav-brand">
        <a href="<?= BASE_URL ?>/index.php" style="color: var(--primary-color);">EduPortal Academy</a>
    </div>
    <div class="nav-links">
        <?php if (is_logged_in()) { ?>
            <span class="nav-link">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="<?= BASE_URL ?>/auth/logout.php" class="btn btn-danger">Logout</a>
        <?php } else { ?>
            <a href="<?= BASE_URL ?>/auth/login.php" class="nav-link">Login</a>
            <a href="<?= BASE_URL ?>/auth/register.php" class="btn btn-primary">Register</a>
        <?php } ?>
    </div>
</nav>
