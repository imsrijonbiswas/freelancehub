<?php
// FreelanceHub/freelancer/actions/send_message.php
session_name("FREELANCER_SESSION");
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = intval($_POST['job_id']);
    $message = trim($_POST['message']);
    $freelancer_id = $_SESSION['freelancer_id'];
    
    if (empty($message)) {
        header('Location: ../dashboard.php?page=messages&job_id=' . $job_id);
        exit();
    }
    
    $check = $conn->prepare("SELECT hire_id FROM hired_freelancers WHERE job_id = ? AND freelancer_id = ?");
    $check->bind_param("ii", $job_id, $freelancer_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        header('Location: ../dashboard.php?page=messages');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_type, sender_id, message_text) VALUES (?, 'freelancer', ?, ?)");
    $stmt->bind_param("iis", $job_id, $freelancer_id, $message);
    $stmt->execute();
    
    header('Location: ../dashboard.php?page=messages&job_id=' . $job_id);
    exit();
} else {
    header('Location: ../dashboard.php?page=messages');
    exit();
}
?>