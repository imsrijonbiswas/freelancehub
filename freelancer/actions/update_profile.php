<?php
// FreelanceHub/freelancer/actions/update_profile.php
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $freelancer_id = $_SESSION['freelancer_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    
    if (empty($full_name) || empty($email)) {
        header('Location: ../dashboard.php?page=profile&error=Name and email are required');
        exit();
    }
    
    // Check if email is already taken by another user
    $check = $conn->prepare("SELECT freelancer_id FROM freelancers WHERE email = ? AND freelancer_id != ?");
    $check->bind_param("si", $email, $freelancer_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        header('Location: ../dashboard.php?page=profile&error=Email already taken');
        exit();
    }
    
    // Update profile
    $stmt = $conn->prepare("UPDATE freelancers SET full_name = ?, email = ?, bio = ? WHERE freelancer_id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $bio, $freelancer_id);
    
    if ($stmt->execute()) {
        // Update session
        $_SESSION['freelancer_name'] = $full_name;
        $_SESSION['freelancer_email'] = $email;
        
        header('Location: ../dashboard.php?page=profile&msg=updated');
    } else {
        header('Location: ../dashboard.php?page=profile&error=Failed to update profile');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=profile');
    exit();
}
?>