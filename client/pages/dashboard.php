<?php
// Example dynamic queries (adjust table names later)

// Total jobs
$jobs = $conn->query("SELECT COUNT(*) total FROM jobs WHERE client_id=$clientId");
$totalJobs = $jobs->fetch_assoc()['total'] ?? 0;

// Unread messages
$msg = $conn->query("SELECT COUNT(*) total FROM messages WHERE client_id=$clientId AND seen=0");
$unread = $msg->fetch_assoc()['total'] ?? 0;
?>

<h1>Dashboard</h1>

<div class="stats">
    <div class="card">Total Jobs: <?= $totalJobs ?></div>
    <div class="card">Unread Messages: <?= $unread ?></div>
</div>
