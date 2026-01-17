<?php
// FreelanceHub/admin/actions/delete_course.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['id'])) {
    $course_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=courses&msg=deleted');
    } else {
        header('Location: ../dashboard.php?page=courses&error=Failed to delete course');
    }
} else {
    header('Location: ../dashboard.php?page=courses');
}
exit();
?>