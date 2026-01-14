<?php
$users   = $conn->query("SELECT COUNT(*) total FROM clients")->fetch_assoc()['total'];
$skills  = $conn->query("SELECT COUNT(*) total FROM skill_categories")->fetch_assoc()['total'];
$courses = $conn->query("SELECT COUNT(*) total FROM courses")->fetch_assoc()['total'];

$frauds = $conn->query("
    SELECT full_name, email, created_at
    FROM clients
    ORDER BY created_at DESC
    LIMIT 5
");
?>

<h1>Dashboard</h1>
<p class="subtitle">Overview</p>

<div class="stats">
    <div class="stat-box"><?= $users ?><span>Users</span></div>
    <div class="stat-box"><?= $skills ?><span>Skills</span></div>
    <div class="stat-box"><?= $courses ?><span>Courses</span></div>
</div>

<h3>Users With Fraud</h3>

<table class="data-table">
    <tr>
        <th>User</th>
        <th>Email</th>
        <th>Joined</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $frauds->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= date('F d, Y', strtotime($row['created_at'])) ?></td>
        <td><a href="#" class="btn-danger">Suspend</a></td>
    </tr>
    <?php endwhile; ?>
</table>
