<?php
session_start();

// If user not logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit();
}

// Optional: restrict role access
function checkRole($requiredRole)
{
    if ($_SESSION['user_role'] !== $requiredRole) {
        header("Location: ../unauthorized.php");
        exit();
    }
}
?>
