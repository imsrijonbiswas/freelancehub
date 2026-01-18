<?php
// FreelanceHub/client/actions/hire_freelancer.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['job_id']) && isset($_GET['freelancer_id'])) {
    $job_id = intval($_GET['job_id']);
    $freelancer_id = intval($_GET['freelancer_id']);
    $client_id = $_SESSION['client_id'];
    
    $check = $conn->prepare("SELECT job_id, hired_freelancer_id FROM jobs WHERE job_id = ? AND client_id = ?");
    $check->bind_param("ii", $job_id, $client_id);
    $check->execute();
    $job = $check->get_result()->fetch_assoc();
    
    if (!$job) {
        header('Location: ../dashboard.php?page=jobs&error=Unauthorized');
        exit();
    }
    
    if ($job['hired_freelancer_id']) {
        header('Location: ../dashboard.php?page=jobs&view_applications=' . $job_id . '&error=Already hired someone for this job');
        exit();
    }
    
    // Hire freelancer
    $stmt = $conn->prepare("INSERT INTO hired_freelancers (job_id, freelancer_id, client_id, status) VALUES (?, ?, ?, 'active')");
    $stmt->bind_param("iii", $job_id, $freelancer_id, $client_id);
    
    if ($stmt->execute()) {
        $update = $conn->prepare("UPDATE jobs SET hired_freelancer_id = ?, status = 'closed' WHERE job_id = ?");
        $update->bind_param("ii", $freelancer_id, $job_id);
        $update->execute();
        
        header('Location: ../dashboard.php?page=jobs&msg=hired');
    } else {
        header('Location: ../dashboard.php?page=jobs&error=Failed to hire freelancer');
    }
} else {
    header('Location: ../dashboard.php?page=jobs');
}
exit();
?>