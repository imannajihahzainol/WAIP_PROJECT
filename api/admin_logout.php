<?php
session_start();
$_SESSION['admin_logged_in'] = false;
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
session_destroy();
header('Location: ../admin_login.php'); 
exit;
?>