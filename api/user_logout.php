<?php
session_start();
$_SESSION['customer_logged_in'] = false;
unset($_SESSION['customer_id']);
session_destroy();
header('Location: ../user_login.php'); 
exit;
?>