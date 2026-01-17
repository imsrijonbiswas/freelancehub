<?php
// FreelanceHub/admin/actions/delete_skill.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['id'])) {
    $skill_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM skill_categories WHERE skill_id = ?");
    $stmt->bind_param("i", $skill_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=skills&msg=deleted');
    } else {
        header('Location: ../dashboard.php?page=skills&error=Failed to delete skill');
    }
} else {
    header('Location: ../dashboard.php?page=skills');
}
exit();
?>