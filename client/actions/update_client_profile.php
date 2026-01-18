<?php
// FreelanceHub/client/actions/update_client_profile.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_SESSION['client_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    
    if (empty($full_name) || empty($email)) {
        header('Location: ../dashboard.php?page=settings&error=Name and email are required');
        exit();
    }
    
    $check = $conn->prepare("SELECT client_id FROM clients WHERE email = ? AND client_id != ?");
    $check->bind_param("si", $email, $client_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        header('Location: ../dashboard.php?page=settings&error=Email already taken');
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE clients SET full_name = ?, email = ? WHERE client_id = ?");
    $stmt->bind_param("ssi", $full_name, $email, $client_id);
    
    if ($stmt->execute()) {
        $_SESSION['client_name'] = $full_name;
        $_SESSION['client_email'] = $email;
        
        header('Location: ../dashboard.php?page=settings&msg=updated');
    } else {
        header('Location: ../dashboard.php?page=settings&error=Failed to update profile');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=settings');
    exit();
}
?>