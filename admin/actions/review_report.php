<?php
// FreelanceHub/admin/actions/review_report.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

$report_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("UPDATE user_reports SET status = 'reviewed' WHERE report_id = ?");
$stmt->bind_param("i", $report_id);

if ($stmt->execute()) {
    header('Location: ../dashboard.php?page=dashboard&msg=reviewed');
} else {
    header('Location: ../dashboard.php?page=dashboard&error=Failed to update report');
}

$stmt->close();
$conn->close();
exit();
?>