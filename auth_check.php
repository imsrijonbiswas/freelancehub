<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: ../login.php");
    exit();
}

function checkRole($requiredRole)
{
    if ($_SESSION['user_role'] !== $requiredRole) {
        header("Location: ../unauthorized.php");
        exit();
    }
}
?>
