<?php
session_start();

$pageCSS = 'client/client.css';
include '../header.php';
include '../db.php';

// fake user id for now
$clientId = 1;

$page = $_GET['page'] ?? 'dashboard';

$allowed = [
    'dashboard',
    'jobs',
    'freelancers',
    'messages',
    'payments',
    'settings'
];

if (!in_array($page, $allowed)) {
    $page = 'dashboard';
}
?>

<div class="client-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h3>Mr. X</h3>

        <a href="?page=dashboard">Dashboard</a>
        <a href="?page=jobs">Jobs</a>
        <a href="?page=freelancers">Freelancers</a>
        <a href="?page=messages">Messages</a>
        <a href="?page=payments">Payments</a>
        <a href="?page=settings">Settings</a>

        <a href="../logout.php" class="logout">Logout</a>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <?php include "pages/$page.php"; ?>
    </main>

</div>

<?php include '../footer.php'; ?>
