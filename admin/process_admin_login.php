<?php
// FreelanceHub/admin/process_admin_login.php

session_name("ADMIN_SESSION");
session_start();

require '../db_config.php';

$email = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare(
    "SELECT admin_id, full_name FROM admins WHERE email = ? AND password = ?"
);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['full_name'];

    header("Location: dashboard.php");
    exit;
}

header("Location: adminLogin.php?error=1");
exit;
