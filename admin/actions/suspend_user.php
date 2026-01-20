<?php
// FreelanceHub/admin/actions/suspend_user.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

$user_type = $_GET['type'] ?? '';
$user_id = $_GET['id'] ?? 0;
$report_id = $_GET['report'] ?? 0;

$conn->begin_transaction();

try {
    if ($user_type === 'freelancer') {
        $stmt = $conn->prepare("UPDATE freelancers SET is_suspended = TRUE WHERE freelancer_id = ?");
    } elseif ($user_type === 'client') {
        $stmt = $conn->prepare("UPDATE clients SET is_suspended = TRUE WHERE client_id = ?");
    } else {
        throw new Exception("Invalid user type");
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    if ($report_id) {
        $stmt2 = $conn->prepare("UPDATE user_reports SET status = 'resolved' WHERE report_id = ?");
        $stmt2->bind_param("i", $report_id);
        $stmt2->execute();
    }
    
    $conn->commit();
    header('Location: ../dashboard.php?page=dashboard&msg=suspended');
    
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../dashboard.php?page=dashboard&error=Failed to suspend user');
}

$conn->close();
exit();
?>