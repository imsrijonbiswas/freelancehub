<?php
// FreelanceHub/admin/actions/toggle_suspend.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

$user_id = $_GET['id'] ?? 0;
$user_type = $_GET['type'] ?? '';
$action = $_GET['action'] ?? ''; // suspend or activate

if ($action == 'suspend') {
    $is_suspended = 1;
    $msg = 'suspended';
} elseif ($action == 'activate') {
    $is_suspended = 0;
    $msg = 'activated';
} else {
    header('Location: ../dashboard.php?page=users&error=Invalid action');
    exit();
}

if ($user_type == 'freelancer') {
    $stmt = $conn->prepare("UPDATE freelancers SET is_suspended = ? WHERE freelancer_id = ?");
} elseif ($user_type == 'client') {
    $stmt = $conn->prepare("UPDATE clients SET is_suspended = ? WHERE client_id = ?");
} else {
    header('Location: ../dashboard.php?page=users&error=Invalid user type');
    exit();
}

$stmt->bind_param("ii", $is_suspended, $user_id);

if ($stmt->execute()) {
    header('Location: ../dashboard.php?page=users&msg=' . $msg);
} else {
    header('Location: ../dashboard.php?page=users&error=Operation failed');
}

$stmt->close();
$conn->close();
exit();
?>