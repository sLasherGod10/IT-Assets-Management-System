<?php
session_start();

// Destroy all session variables
$_SESSION = [];
session_destroy();

// Redirect to login page
header("Location: login.php?logged_out=1");
exit();
?>
