// FreelanceHub/freelancer/actions/report_client.php

<?php
session_name("FREELANCER_SESSION");
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

$freelancer_id = $_SESSION['freelancer_id'];
$client_id = $_POST['client_id'] ?? 0;
$job_id = $_POST['job_id'] ?? 0;
$report_reason = trim($_POST['report_reason'] ?? '');
$report_message = trim($_POST['report_message'] ?? '');

if (empty($report_reason) || empty($report_message) || empty($job_id) || empty($client_id)) {
    header('Location: ../dashboard.php?page=applications&error=All fields are required');
    exit();
}

$check_stmt = $conn->prepare("
    SELECT report_id FROM user_reports 
    WHERE job_id = ? AND reported_user_id = ? AND reporter_id = ? AND reporter_type = 'freelancer'
");
$check_stmt->bind_param("iii", $job_id, $client_id, $freelancer_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $check_stmt->close();
    $conn->close();
    header('Location: ../dashboard.php?page=applications&error=You have already reported this client for this job');
    exit();
}
$check_stmt->close();

$verify_stmt = $conn->prepare("
    SELECT hire_id FROM hired_freelancers 
    WHERE job_id = ? AND client_id = ? AND freelancer_id = ?
");
$verify_stmt->bind_param("iii", $job_id, $client_id, $freelancer_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    $verify_stmt->close();
    $conn->close();
    header('Location: ../dashboard.php?page=applications&error=You can only report clients you have worked with');
    exit();
}
$verify_stmt->close();

$stmt = $conn->prepare("
    INSERT INTO user_reports 
    (reported_user_id, reported_user_type, reporter_id, reporter_type, job_id, report_reason, report_message, status, created_at) 
    VALUES (?, 'client', ?, 'freelancer', ?, ?, ?, 'pending', NOW())
");
$stmt->bind_param("iiiss", $client_id, $freelancer_id, $job_id, $report_reason, $report_message);

try {
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: ../dashboard.php?page=applications&msg=reported');
        exit();
    } else {
        throw new Exception("Failed to insert report");
    }
} catch (Exception $e) {
    $stmt->close();
    $conn->close();
    header('Location: ../dashboard.php?page=applications&error=Failed to submit report. Please try again.');
    exit();
}
?>