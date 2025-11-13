<?php
// Start the session before using session functions
session_start();

// Unset all session variables associated with the admin login
$_SESSION['admin_logged_in'] = false;
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
// You can unset the entire array, but unsetting specific keys is often safer.

// Finally, destroy the session (cleans up the session file on the server)
session_destroy();

// Redirect the user to the admin login page
header('Location: ../admin_login.php'); 
// NOTE: Use '../admin_login.php' to step out of the 'api/' folder 
// to reach the correct admin login page.
exit;
?>