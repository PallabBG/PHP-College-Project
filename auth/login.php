<?php
// auth/login.php
require_once '../config/database.php';
require_once '../includes/functions.php';

if (is_logged_in()) {
    $role = get_user_role();
    redirect("../{$role}/dashboard.php");
}

$error = '';

if (isset($_POST['submit'])) {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        global $conn;
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_row($result);

        if ($user && password_verify($password, $user[3])) {
            $_SESSION['user_id'] = $user[0];
            $_SESSION['user_name'] = $user[1];
            $_SESSION['user_role'] = $user[4];

            set_flash_message('success', 'Welcome back, ' . $user[1] . '!');
            redirect("../{$user[4]}/dashboard.php");
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Learning Platform</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2>Login</h2>
            <?php if ($error) { ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php } ?>
            <?php display_flash_messages(); ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="auth-redirect">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
