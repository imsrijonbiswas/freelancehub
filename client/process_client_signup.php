<?php
// FreelanceHub/client/process_client_signup.php
session_name("CLIENT_SESSION");
session_start();
require '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // No hashing
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($fullname) || empty($email) || empty($password)) {
        header('Location: clientSignup.php?error=All fields are required');
        exit();
    }
    
    if ($password !== $confirm_password) {
        header('Location: clientSignup.php?error=Passwords do not match');
        exit();
    }
    
    if (strlen($password) < 6) {
        header('Location: clientSignup.php?error=Password must be at least 6 characters');
        exit();
    }
    
    $stmt = $conn->prepare("SELECT client_id FROM clients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Location: clientSignup.php?error=Email already registered');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO clients (full_name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $password);
    
    if ($stmt->execute()) {
        header('Location: clientSignup.php?success=Account created successfully! Please login.');
        exit();
    } else {
        header('Location: clientSignup.php?error=Failed to create account. Please try again.');
        exit();
    }
    
} else {
    header('Location: clientSignup.php');
    exit();
}
?>