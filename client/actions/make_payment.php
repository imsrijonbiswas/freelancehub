<?php
// FreelanceHub/client/actions/make_payment.php
session_name("CLIENT_SESSION");
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: ../../login.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hire_id = intval($_POST['hire_id']);
    $amount = floatval($_POST['amount']);
    $client_id = $_SESSION['client_id'];
    
    if ($amount <= 0) {
        header('Location: ../dashboard.php?page=payments&error=Invalid amount');
        exit();
    }
    
    $check = $conn->prepare("SELECT hire_id FROM hired_freelancers WHERE hire_id = ? AND client_id = ?");
    $check->bind_param("ii", $hire_id, $client_id);
    $check->execute();
    
    if ($check->get_result()->num_rows == 0) {
        header('Location: ../dashboard.php?page=payments&error=Unauthorized');
        exit();
    }
    
    $check_payment = $conn->prepare("SELECT payment_id FROM payments WHERE hire_id = ?");
    $check_payment->bind_param("i", $hire_id);
    $check_payment->execute();
    
    if ($check_payment->get_result()->num_rows > 0) {
        header('Location: ../dashboard.php?page=payments&error=Already paid');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO payments (hire_id, amount, payment_status) VALUES (?, ?, 'completed')");
    $stmt->bind_param("id", $hire_id, $amount);
    
    if ($stmt->execute()) {
        $update = $conn->prepare("UPDATE hired_freelancers SET status = 'completed' WHERE hire_id = ?");
        $update->bind_param("i", $hire_id);
        $update->execute();
        
        header('Location: ../dashboard.php?page=payments&msg=paid');
    } else {
        header('Location: ../dashboard.php?page=payments&error=Payment failed');
    }
    exit();
} else {
    header('Location: ../dashboard.php?page=payments');
    exit();
}
?>