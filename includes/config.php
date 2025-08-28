<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'phonebook');
define('DB_USER', 'root');
define('DB_PASS', '');
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); 


$allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
?>