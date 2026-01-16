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
    
    // Verify job belongs to client
    $check = $conn->prepare("SELECT job_id FROM jobs WHERE job_id = ? AND client_id = ?");
    $check->bind_param("ii", $job_id, $client_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        header('Location: ../dashboard.php?page=jobs&error=Unauthorized');
        exit();
    }
    
    // Check if already hired
    $check_hired = $conn->prepare("SELECT hire_id FROM hired_freelancers WHERE job_id = ? AND freelancer_id = ?");
    $check_hired->bind_param("ii", $job_id, $freelancer_id);
    $check_hired->execute();
    
    if ($check_hired->get_result()->num_rows > 0) {
        header('Location: ../dashboard.php?page=jobs&view_applications=' . $job_id . '&error=Already hired');
        exit();
    }
    
    // Hire freelancer
    $stmt = $conn->prepare("INSERT INTO hired_freelancers (job_id, freelancer_id, client_id, status) VALUES (?, ?, ?, 'active')");
    $stmt->bind_param("iii", $job_id, $freelancer_id, $client_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=jobs&msg=hired');
    } else {
        header('Location: ../dashboard.php?page=jobs&error=Failed to hire freelancer');
    }
} else {
    header('Location: ../dashboard.php?page=jobs');
}
exit();
?>