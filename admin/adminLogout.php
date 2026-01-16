<?php
// FreelanceHub/admin/adminlogout.php
session_name("ADMIN_SESSION");
session_start();
session_unset();
session_destroy();

header('Location: adminLogin.php');
exit();
?>