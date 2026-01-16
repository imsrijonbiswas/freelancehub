//admin/admin_auth_check.php
<?php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminLogin.php");
    exit;
}
