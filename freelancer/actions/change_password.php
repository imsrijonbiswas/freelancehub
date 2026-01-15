<?php
// FreelanceHub/freelancer/actions/change_password.php
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $freelancer_id = $_SESSION['freelancer_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password
    $stmt = $conn->prepare("SELECT password FROM freelancers WHERE freelancer_id = ?");
    $stmt->bind_param("i", $freelancer_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    // Verify current password (no hashing)
    if ($current_password !== $result['password']) {
        header('Location: ../dashboard.php?page=profile&error=Current password is incorrect');
        exit();
    }
    
    // Validate new password
    if ($new_password !== $confirm_password) {
        header('Location: ../dashboard.php?page=profile&error=New passwords do not match');
        exit();
    }
    
    if (strlen($new_password) < 6) {
        header('Location: ../dashboard.php?page=profile&error=Password must be at least 6 characters');
        exit();
    }
    
    // Update password (no hashing)
    $stmt = $conn->prepare("UPDATE freelancers SET password = ? WHERE freelancer_id = ?");
    $stmt->bind_param("si", $new_password, $freelancer_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=profile&msg=updated');
    } else {
        header('Location: ../dashboard.php?page=profile&error=Failed to change password');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=profile');
    exit();
}
?>