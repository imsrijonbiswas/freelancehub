<?php
// FreelanceHub/admin/pages/dashboard.php

$freelancers_query = $conn->query("SELECT COUNT(*) as total FROM freelancers");
$clients_query = $conn->query("SELECT COUNT(*) as total FROM clients");
$total_freelancers = $freelancers_query ? $freelancers_query->fetch_assoc()['total'] : 0;
$total_clients = $clients_query ? $clients_query->fetch_assoc()['total'] : 0;
$total_users = $total_freelancers + $total_clients;

$skills_query = $conn->query("SELECT COUNT(*) as total FROM skill_categories");
$total_skills = $skills_query ? $skills_query->fetch_assoc()['total'] : 0;

$courses_query = $conn->query("SELECT COUNT(*) as total FROM courses");
$total_courses = $courses_query ? $courses_query->fetch_assoc()['total'] : 0;

$fraud_query = $conn->query("
    SELECT 
        f.freelancer_id,
        f.full_name,
        f.email,
        f.created_at,
        COUNT(r.report_id) as report_count
    FROM user_reports r
    JOIN freelancers f ON f.freelancer_id = r.reported_user_id
    WHERE r.status = 'pending'
    GROUP BY f.freelancer_id
    ORDER BY report_count DESC, r.created_at DESC
    LIMIT 10
");
?>

<div class="dashboard-section">
    <h2 class="section-title">Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-number"><?= $total_users ?></span>
            <span class="stat-label">Users</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $total_skills ?></span>
            <span class="stat-label">Skills</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $total_courses ?></span>
            <span class="stat-label">Courses</span>
        </div>
    </div>
</div>

<div class="dashboard-section">
    <div class="fraud-section">
        <h2 class="section-title">Users With Fraud</h2>
        
        <?php if ($fraud_query && $fraud_query->num_rows > 0): ?>
            <div class="fraud-table-wrapper">
                <table class="fraud-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $fraud_query->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= date('F d, Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="actions/suspend_user.php?id=<?= $row['freelancer_id'] ?>" 
                                       class="btn-suspend"
                                       onclick="return confirm('Are you sure you want to suspend this user?')">
                                        Suspend
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                    <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <p>No reported users at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>