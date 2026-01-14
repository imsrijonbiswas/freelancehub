<?php
require '../db_config.php';

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if ($password !== $confirm) {
    die('Passwords do not match');
}

$check = $conn->prepare("SELECT admin_id FROM admins WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die('Admin already exists');
}

$stmt = $conn->prepare(
    "INSERT INTO admins (full_name, email, password) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $fullname, $email, $password);
$stmt->execute();

header("Location: adminSignup.php?success=1");
exit;
