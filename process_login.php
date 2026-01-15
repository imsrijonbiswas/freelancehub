<?php
// FreelanceHub/process_login.php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        header('Location: login.php?error=All fields are required');
        exit();
    }
    
    // First, check if user is a freelancer
    $stmt = $conn->prepare("
        SELECT f.freelancer_id, f.full_name, f.email, f.password, f.skill_id, f.is_verified, s.skill_name
        FROM freelancers f
        LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
        WHERE f.email = ?
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User is a freelancer
        $freelancer = $result->fetch_assoc();
        
        // Check password (no hashing)
        if ($password !== $freelancer['password']) {
            header('Location: login.php?error=Invalid email or password');
            exit();
        }
        
        // Check if verified
        if (!$freelancer['is_verified']) {
            header('Location: login.php?error=Your account is not verified. Please complete the skill test.');
            exit();
        }
        
        // Set freelancer session
        $_SESSION['freelancer_id'] = $freelancer['freelancer_id'];
        $_SESSION['freelancer_name'] = $freelancer['full_name'];
        $_SESSION['freelancer_email'] = $freelancer['email'];
        $_SESSION['freelancer_skill_id'] = $freelancer['skill_id'];
        $_SESSION['freelancer_skill_name'] = $freelancer['skill_name'];
        
        header('Location: freelancer/dashboard.php');
        exit();
    }
    
    // If not freelancer, check if user is a client
    $stmt = $conn->prepare("SELECT client_id, full_name, email, password FROM clients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User is a client
        $client = $result->fetch_assoc();
        
        // Check password (no hashing)
        if ($password !== $client['password']) {
            header('Location: login.php?error=Invalid email or password');
            exit();
        }
        
        // Set client session
        $_SESSION['client_id'] = $client['client_id'];
        $_SESSION['client_name'] = $client['full_name'];
        $_SESSION['client_email'] = $client['email'];
        
        header('Location: client/dashboard.php');
        exit();
    }
    
    // No user found with this email
    header('Location: login.php?error=Invalid email or password');
    exit();
    
} else {
    header('Location: login.php');
    exit();
}
?>