<?php
// FreelanceHub/process_login.php

require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    header('Location: login.php?error=All fields required');
    exit();
}

// FREELANCER LOGIN
$stmt = $conn->prepare("
    SELECT 
        f.freelancer_id,
        f.full_name,
        f.email,
        f.password,
        f.skill_id,
        f.is_verified,
        f.is_suspended,
        s.skill_name
    FROM freelancers f
    LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
    WHERE f.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $freelancer = $result->fetch_assoc();

    if ($password !== $freelancer['password']) {
        header('Location: login.php?error=Invalid email or password');
        exit();
    }

    if ((int)$freelancer['is_suspended'] === 1) {
        header('Location: login.php?error=Your account has been suspended. Please contact support.');
        exit();
    }

    if ((int)$freelancer['is_verified'] === 0) {
        header('Location: login.php?error=Complete skill test first');
        exit();
    }

    session_name("FREELANCER_SESSION");
    session_start();

    $_SESSION['freelancer_id']        = $freelancer['freelancer_id'];
    $_SESSION['freelancer_name']      = $freelancer['full_name'];
    $_SESSION['freelancer_email']     = $freelancer['email'];
    $_SESSION['freelancer_skill_id']  = $freelancer['skill_id'];
    $_SESSION['freelancer_skill_name']= $freelancer['skill_name'];

    header('Location: freelancer/dashboard.php');
    exit();
}


// CLIENT LOGIN
$stmt = $conn->prepare("
    SELECT 
        client_id,
        full_name,
        email,
        password,
        is_suspended
    FROM clients
    WHERE email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $client = $result->fetch_assoc();

    if ($password !== $client['password']) {
        header('Location: login.php?error=Invalid email or password');
        exit();
    }

    if ((int)$client['is_suspended'] === 1) {
        header('Location: login.php?error=Your account has been suspended. Please contact support.');
        exit();
    }

    session_name("CLIENT_SESSION");
    session_start();

    $_SESSION['client_id']    = $client['client_id'];
    $_SESSION['client_name']  = $client['full_name'];
    $_SESSION['client_email'] = $client['email'];

    header('Location: client/dashboard.php');
    exit();
}

// INVALID LOGIN
header('Location: login.php?error=Invalid email or password');
exit();
