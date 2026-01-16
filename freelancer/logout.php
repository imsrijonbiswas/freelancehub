<?php
// FreelanceHub/freelancer/logout.php
session_name("FREELANCER_SESSION");
session_start();
session_unset();
session_destroy();

header('Location: ../login.php');
exit();
?>