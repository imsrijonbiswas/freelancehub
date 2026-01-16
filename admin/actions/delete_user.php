<?php
// FreelanceHub/admin/actions/delete_user.php
session_name("ADMIN_SESSION");
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $user_id = intval($_GET['id']);
    $user_type = $_GET['type'];
    
    if ($user_type == 'freelancer') {
        // Delete freelancer
        $stmt = $conn->prepare("DELETE FROM freelancers WHERE freelancer_id = ?");
        $stmt->bind_param("i", $user_id);
    } elseif ($user_type == 'client') {
        // Delete client
        $stmt = $conn->prepare("DELETE FROM clients WHERE client_id = ?");
        $stmt->bind_param("i", $user_id);
    } else {
        header('Location: ../dashboard.php?page=users&error=Invalid user type');
        exit();
    }
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=users&msg=deleted');
    } else {
        header('Location: ../dashboard.php?page=users&error=Failed to delete user');
    }
} else {
    header('Location: ../dashboard.php?page=users');
}
exit();
?>