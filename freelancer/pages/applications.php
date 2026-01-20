<?php
// FreelanceHub/freelancer/pages/applications.php

$freelancer_id = $_SESSION['freelancer_id'];

$apps_query = $conn->query("
    SELECT 
        ja.application_id,
        ja.applied_at,
        j.job_title,
        j.job_description,
        j.budget,
        j.status as job_status,
        c.client_id,
        c.full_name as client_name,
        s.skill_name,
        CASE 
            WHEN hf.hire_id IS NOT NULL THEN 'hired'
            WHEN j.status = 'closed' THEN 'rejected'
            ELSE 'pending'
        END as application_status
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.job_id
    JOIN clients c ON j.client_id = c.client_id
    JOIN skill_categories s ON j.skill_id = s.skill_id
    LEFT JOIN hired_freelancers hf ON ja.job_id = hf.job_id AND ja.freelancer_id = hf.freelancer_id
    WHERE ja.freelancer_id = $freelancer_id
    ORDER BY ja.applied_at DESC
");
?>

<div class="applications-container">
    <?php if ($apps_query && $apps_query->num_rows > 0): ?>
        <div class="applications-list">
            <?php while ($app = $apps_query->fetch_assoc()): ?>
                <div class="application-card status-<?= $app['application_status'] ?>">
                    <div class="app-header">
                        <div>
                            <h3 class="app-job-title"><?= htmlspecialchars($app['job_title']) ?></h3>
                            <span class="app-client">
                                by <a href="dashboard.php?page=client_profile&id=<?= $app['client_id'] ?>" class="client-name-link"><?= htmlspecialchars($app['client_name']) ?></a>
                            </span>
                        </div>
                        <span class="status-badge <?= $app['application_status'] ?>">
                            <?= ucfirst($app['application_status']) ?>
                        </span>
                    </div>
                    
                    <p class="app-description"><?= nl2br(htmlspecialchars(substr($app['job_description'], 0, 200))) ?>...</p>
                    
                    <div class="app-footer">
                        <div class="app-meta">
                            <span class="skill-tag"><?= htmlspecialchars($app['skill_name']) ?></span>
                            <span class="budget-tag">$<?= number_format($app['budget'], 2) ?></span>
                        </div>
                        <span class="applied-date">Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></span>
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
            <p>No applications yet. Start browsing jobs!</p>
            <a href="dashboard.php?page=jobs" class="btn-primary">Browse Jobs</a>
        </div>
    <?php endif; ?>
</div>

<link rel="stylesheet" href="css/applications.css">