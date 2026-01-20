<?php
// FreelanceHub/freelancer/actions/report_client.php
session_name("FREELANCER_SESSION");
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

$freelancer_id = $_SESSION['freelancer_id'];
$client_id = $_POST['client_id'] ?? 0;
$report_reason = $_POST['report_reason'] ?? '';
$report_message = $_POST['report_message'] ?? '';

if (empty($report_reason) || empty($report_message)) {
    header('Location: ../dashboard.php?page=client_profile&id=' . $client_id . '&error=All fields are required');
    exit();
}

$verify_stmt = $conn->prepare("
    SELECT hire_id FROM hired_freelancers 
    WHERE client_id = ? AND freelancer_id = ?
");
$verify_stmt->bind_param("ii", $client_id, $freelancer_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows === 0) {
    header('Location: ../dashboard.php?page=client_profile&id=' . $client_id . '&error=You can only report clients you have worked with');
    exit();
}

$check_stmt = $conn->prepare("
    SELECT report_id FROM user_reports 
    WHERE reported_user_id = ? AND reported_user_type = 'client' 
    AND reporter_id = ? AND reporter_type = 'freelancer'
");
$check_stmt->bind_param("ii", $client_id, $freelancer_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows > 0) {
    header('Location: ../dashboard.php?page=client_profile&id=' . $client_id . '&error=You have already reported this client');
    exit();
}

$stmt = $conn->prepare("
    INSERT INTO user_reports 
    (reported_user_id, reported_user_type, reporter_id, reporter_type, report_reason, report_message, status, created_at) 
    VALUES (?, 'client', ?, 'freelancer', ?, ?, 'pending', NOW())
");
$stmt->bind_param("iiss", $client_id, $freelancer_id, $report_reason, $report_message);

if ($stmt->execute()) {
    header('Location: ../dashboard.php?page=client_profile&id=' . $client_id . '&msg=reported');
} else {
    header('Location: ../dashboard.php?page=client_profile&id=' . $client_id . '&error=Failed to submit report');
}

$stmt->close();
$conn->close();
exit();
?>