<?php
// FreelanceHub/client/actions/send_message.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = intval($_POST['job_id']);
    $message = trim($_POST['message']);
    $client_id = $_SESSION['client_id'];
    
    if (empty($message)) {
        header('Location: ../dashboard.php?page=messages&job_id=' . $job_id);
        exit();
    }
    
    $check = $conn->prepare("SELECT hire_id FROM hired_freelancers WHERE job_id = ? AND client_id = ?");
    $check->bind_param("ii", $job_id, $client_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        header('Location: ../dashboard.php?page=messages');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO messages (job_id, sender_type, sender_id, message_text) VALUES (?, 'client', ?, ?)");
    $stmt->bind_param("iis", $job_id, $client_id, $message);
    $stmt->execute();
    
    header('Location: ../dashboard.php?page=messages&job_id=' . $job_id);
    exit();
} else {
    header('Location: ../dashboard.php?page=messages');
    exit();
}
?>