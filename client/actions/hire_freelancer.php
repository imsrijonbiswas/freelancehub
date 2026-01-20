<?php
// FreelanceHub/client/actions/hire_freelancer.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

$job_id = $_GET['job_id'] ?? 0;
$freelancer_id = $_GET['freelancer_id'] ?? 0;
$client_id = $_SESSION['client_id'];

$check_query = $conn->prepare("SELECT is_hired, hired_freelancer_id FROM jobs WHERE job_id = ? AND client_id = ?");
$check_query->bind_param("ii", $job_id, $client_id);
$check_query->execute();
$result = $check_query->get_result();

if ($result->num_rows === 0) {
    header('Location: ../dashboard.php?page=jobs&error=Job not found');
    exit();
}

$job = $result->fetch_assoc();

if ($job['is_hired'] || $job['hired_freelancer_id']) {
    header('Location: ../dashboard.php?page=jobs&msg=already_hired');
    exit();
}

$conn->begin_transaction();

try {
    $update_job = $conn->prepare("
        UPDATE jobs 
        SET hired_freelancer_id = ?, 
            is_hired = TRUE, 
            hired_at = NOW(),
            status = 'closed'
        WHERE job_id = ?
    ");
    $update_job->bind_param("ii", $freelancer_id, $job_id);
    $update_job->execute();
    
    $insert_hire = $conn->prepare("
        INSERT INTO hired_freelancers (job_id, freelancer_id, client_id, hired_at, status)
        VALUES (?, ?, ?, NOW(), 'active')
    ");
    $insert_hire->bind_param("iii", $job_id, $freelancer_id, $client_id);
    $insert_hire->execute();
    
    $conn->commit();
    
    header('Location: ../dashboard.php?page=jobs&msg=hired');
    
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../dashboard.php?page=jobs&error=Failed to hire freelancer');
}

$conn->close();
exit();
?>