<?php
// Start the session before using session functions
session_start();

// Unset specific session variables related to the customer login status
$_SESSION['customer_logged_in'] = false;
unset($_SESSION['customer_id']);
// You can unset the entire array, but unsetting specific keys is often cleaner.

// Destroy the session (cleans up the session file on the server)
session_destroy();

// Redirect the user to the public homepage or login page
// We redirect to main.php, stepping out of the 'api/' folder.
header('Location: ../user_login.php'); 
exit;
?>