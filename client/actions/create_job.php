<?php
// FreelanceHub/client/actions/create_job.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_SESSION['client_id'];
    $job_title = trim($_POST['job_title']);
    $skill_id = intval($_POST['skill_id']);
    $job_description = trim($_POST['job_description']);
    $budget = floatval($_POST['budget']);
    
    if (empty($job_title) || empty($job_description) || $skill_id == 0 || $budget <= 0) {
        header('Location: ../dashboard.php?page=post-job&error=All fields are required');
        exit();
    }
    
    $stmt = $conn->prepare("
        INSERT INTO jobs (client_id, skill_id, job_title, job_description, budget, status) 
        VALUES (?, ?, ?, ?, ?, 'open')
    ");
    $stmt->bind_param("iissd", $client_id, $skill_id, $job_title, $job_description, $budget);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=post-job&msg=posted');
    } else {
        header('Location: ../dashboard.php?page=post-job&error=Failed to post job');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=post-job');
    exit();
}
?>