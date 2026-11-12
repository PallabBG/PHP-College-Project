<?php
// auth/register.php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (is_logged_in()) {
    $role = get_user_role();
    redirect("../{$role}/dashboard.php");
}

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = sanitize_input($_POST['role']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!in_array($role, ['student', 'instructor'])) {
        $error = 'Invalid role selected.';
    } else {
        global $conn;
        // Check if email exists
        $query = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if (mysqli_fetch_row($result)) {
            $error = 'Email is already registered.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            if (mysqli_query($conn, $query)) {
                set_flash_message('success', 'Registration successful. Please log in.');
                redirect('login.php');
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - E-Learning Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Register</h2>
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <form method="POST" action="register.php">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="role">Register as</label>
                    <select id="role" name="role" required>
                        <option value="student" <?= (isset($role) && $role === 'student') ? 'selected' : '' ?>>Student</option>
                        <option value="instructor" <?= (isset($role) && $role === 'instructor') ? 'selected' : '' ?>>Instructor</option>
                    </select>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p class="auth-redirect">Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
