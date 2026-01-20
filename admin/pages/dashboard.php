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
        r.report_id,
        r.reported_user_id,
        r.reported_user_type,
        r.report_reason,
        r.report_message,
        r.created_at,
        CASE 
            WHEN r.reported_user_type = 'freelancer' THEN f.full_name
            WHEN r.reported_user_type = 'client' THEN c.full_name
        END as reported_user_name,
        CASE 
            WHEN r.reported_user_type = 'freelancer' THEN f.email
            WHEN r.reported_user_type = 'client' THEN c.email
        END as reported_user_email,
        CASE 
            WHEN r.reporter_type = 'freelancer' THEN rf.full_name
            WHEN r.reporter_type = 'client' THEN rc.full_name
            ELSE 'System'
        END as reporter_name,
        COUNT(r.report_id) OVER (PARTITION BY r.reported_user_id) as report_count
    FROM user_reports r
    LEFT JOIN freelancers f ON r.reported_user_id = f.freelancer_id AND r.reported_user_type = 'freelancer'
    LEFT JOIN clients c ON r.reported_user_id = c.client_id AND r.reported_user_type = 'client'
    LEFT JOIN freelancers rf ON r.reporter_id = rf.freelancer_id AND r.reporter_type = 'freelancer'
    LEFT JOIN clients rc ON r.reporter_id = rc.client_id AND r.reporter_type = 'client'
    WHERE r.status = 'pending'
    ORDER BY r.created_at DESC
    LIMIT 15
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
        <h2 class="section-title">Reported Users</h2>
        
        <?php if ($fraud_query && $fraud_query->num_rows > 0): ?>
            <div class="reports-list">
                <?php while ($row = $fraud_query->fetch_assoc()): ?>
                    <div class="report-card">
                        <div class="report-header">
                            <div class="reported-user-info">
                                <h4><?= htmlspecialchars($row['reported_user_name']) ?></h4>
                                <span class="user-type-badge <?= $row['reported_user_type'] ?>">
                                    <?= ucfirst($row['reported_user_type']) ?>
                                </span>
                            </div>
                            <span class="report-date"><?= date('M d, Y', strtotime($row['created_at'])) ?></span>
                        </div>
                        
                        <div class="report-details">
                            <p class="report-email"><?= htmlspecialchars($row['reported_user_email']) ?></p>
                            <div class="report-reason">
                                <strong>Reason:</strong> <?= htmlspecialchars($row['report_reason']) ?>
                            </div>
                            <div class="report-message">
                                <strong>Details:</strong>
                                <p><?= nl2br(htmlspecialchars($row['report_message'])) ?></p>
                            </div>
                            <p class="reporter-info">
                                <small>Reported by: <?= htmlspecialchars($row['reporter_name']) ?></small>
                            </p>
                        </div>
                        
                        <div class="report-actions">
                            <a href="actions/review_report.php?id=<?= $row['report_id'] ?>" 
                               class="btn-review">
                                Mark as Reviewed
                            </a>
                            <a href="actions/suspend_user.php?type=<?= $row['reported_user_type'] ?>&id=<?= $row['reported_user_id'] ?>&report=<?= $row['report_id'] ?>" 
                               class="btn-suspend"
                               onclick="return confirm('Are you sure you want to suspend this user?')">
                                Suspend User
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                    <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <p>No pending reports at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>