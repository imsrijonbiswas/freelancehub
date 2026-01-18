<?php
// FreelanceHub/client/actions/change_client_password.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_SESSION['client_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $stmt = $conn->prepare("SELECT password FROM clients WHERE client_id = ?");
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($current_password !== $result['password']) {
        header('Location: ../dashboard.php?page=settings&error=Current password is incorrect');
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        header('Location: ../dashboard.php?page=settings&error=New passwords do not match');
        exit();
    }
    
    if (strlen($new_password) < 6) {
        header('Location: ../dashboard.php?page=settings&error=Password must be at least 6 characters');
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE clients SET password = ? WHERE client_id = ?");
    $stmt->bind_param("si", $new_password, $client_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=settings&msg=updated');
    } else {
        header('Location: ../dashboard.php?page=settings&error=Failed to change password');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=settings');
    exit();
}
?>