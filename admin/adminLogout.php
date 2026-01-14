<?php
session_start();

// Remove all admin session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: adminLogin.php");
exit;
