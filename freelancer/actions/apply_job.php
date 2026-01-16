<?php
// FreelanceHub/freelancer/actions/apply_job.php
session_name("FREELANCER_SESSION");
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    $freelancer_id = $_SESSION['freelancer_id'];
    
    // Check if already applied
    $check = $conn->prepare("SELECT application_id FROM job_applications WHERE job_id = ? AND freelancer_id = ?");
    $check->bind_param("ii", $job_id, $freelancer_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        header('Location: ../dashboard.php?page=jobs&error=Already applied');
        exit();
    }
    
    // Apply for job
    $stmt = $conn->prepare("INSERT INTO job_applications (job_id, freelancer_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $job_id, $freelancer_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=jobs&msg=applied');
    } else {
        header('Location: ../dashboard.php?page=jobs&error=Failed to apply');
    }
} else {
    header('Location: ../dashboard.php?page=jobs');
}
exit();
?>