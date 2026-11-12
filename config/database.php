<?php
// config/database.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'elearning_platform');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_PORT', 3307);

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>