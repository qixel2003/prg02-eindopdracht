<?php
// Start the session.
session_start();
// destroy the session.
session_destroy();
// Unset all of the session variables
$_SESSION = array();
// Redirect to login page
header('location: login.php');
// Exit the code.
exit();
