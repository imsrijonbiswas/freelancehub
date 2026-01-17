<?php
// FreelanceHub/client/actions/close_job.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    $client_id = $_SESSION['client_id'];
    
    $check = $conn->prepare("SELECT job_id FROM jobs WHERE job_id = ? AND client_id = ?");
    $check->bind_param("ii", $job_id, $client_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        header('Location: ../dashboard.php?page=jobs&error=Unauthorized');
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE jobs SET status = 'closed' WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=jobs&msg=closed');
    } else {
        header('Location: ../dashboard.php?page=jobs&error=Failed to close job');
    }
} else {
    header('Location: ../dashboard.php?page=jobs');
}
exit();
?>