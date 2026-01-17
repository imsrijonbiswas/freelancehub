<?php
// FreelanceHub/freelancer/process_signup.php
session_name("FREELANCER_SESSION");
session_start();
require '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // No hashing
    $confirm_password = $_POST['confirm_password'];
    $skill_id = intval($_POST['skill_id']);
    
    if (empty($fullname) || empty($email) || empty($password) || empty($skill_id)) {
        header('Location: freelancerSignup.php?error=All fields are required');
        exit();
    }
    
    if ($password !== $confirm_password) {
        header('Location: freelancerSignup.php?error=Passwords do not match');
        exit();
    }
    
    if (strlen($password) < 6) {
        header('Location: freelancerSignup.php?error=Password must be at least 6 characters');
        exit();
    }
    
    $stmt = $conn->prepare("SELECT freelancer_id FROM freelancers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Location: freelancerSignup.php?error=Email already registered');
        exit();
    }
    
    $stmt = $conn->prepare("
        SELECT attempt_id FROM freelancer_test_attempts 
        WHERE freelancer_email = ? 
        AND passed = FALSE 
        AND attempted_at > DATE_SUB(NOW(), INTERVAL 1 DAY)
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Location: freelancerSignup.php?error=You failed the test recently. Please try again after 24 hours');
        exit();
    }
    
    $_SESSION['signup_data'] = [
        'fullname' => $fullname,
        'email' => $email,
        'password' => $password,
        'skill_id' => $skill_id
    ];
    
    $stmt = $conn->prepare("SELECT skill_name FROM skill_categories WHERE skill_id = ?");
    $stmt->bind_param("i", $skill_id);
    $stmt->execute();
    $skill_result = $stmt->get_result();
    $skill = $skill_result->fetch_assoc();
    $_SESSION['skill_category'] = $skill['skill_name'];
    
    header('Location: freelancerTestrules.php');
    exit();
    
} else {
    header('Location: freelancerSignup.php');
    exit();
}
?>