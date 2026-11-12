<?php
// includes/role_check.php
// Expected $required_role to be set before including this file
require_once 'auth_check.php';

if (!isset($required_role)) {
    die('Role check configuration error.');
}

if (get_user_role() !== $required_role && get_user_role() !== 'admin') {
    set_flash_message('error', 'You do not have permission to access this page.');
    
    if (get_user_role() === 'student') {
        redirect('/student/dashboard.php');
    } else if (get_user_role() === 'instructor') {
        redirect('/instructor/dashboard.php');
    } else {
        redirect('/index.php');
    }
}
?>
