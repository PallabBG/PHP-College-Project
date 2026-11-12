<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/PHP Project/eduportal-academy-main');

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function set_flash_message($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

function display_flash_messages() {
    if (isset($_SESSION['flash'])) {
        foreach ($_SESSION['flash'] as $type => $message) {
            $alertClass = $type === 'error' ? 'alert-error' : 'alert-success';
            echo '<div class="alert ' . $alertClass . '">' . sanitize_input($message) . '</div>';
        }
        unset($_SESSION['flash']);
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function format_duration($duration) {
    return $duration ? sanitize_input($duration) : 'N/A';
}

function format_date($date) {
    return date('M j, Y', strtotime($date));
}
?>
