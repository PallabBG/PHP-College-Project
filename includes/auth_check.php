<?php
// includes/auth_check.php
require_once 'functions.php';

if (!is_logged_in()) {
    set_flash_message('error', 'Please log in to access this page.');
    redirect('/auth/login.php');
}
?>
