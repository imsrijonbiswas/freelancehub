<?php
// FreelanceHub/freelancer/pages/applications.php

$freelancer_id = $_SESSION['freelancer_id'];

// Get all applications
$apps_query = $conn->query("
    SELECT 
        ja.application_id,
        ja.applied_at,
        j.job_title,
        j.job_description,
        j.budget,
        j.status as job_status,
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
                            <span class="app-client">by <?= htmlspecialchars($app['client_name']) ?></span>
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

<style>
.applications-container {
    padding: 20px 0;
}

.applications-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.application-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #e0e0e0;
}

.application-card.status-hired {
    border-left-color: #4caf50;
    background: linear-gradient(to right, #f1f8f4 0%, #fff 10%);
}

.application-card.status-rejected {
    border-left-color: #ff3b3b;
    background: linear-gradient(to right, #fff0f0 0%, #fff 10%);
}

.application-card.status-pending {
    border-left-color: #ff9800;
    background: linear-gradient(to right, #fff8f0 0%, #fff 10%);
}

.app-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
}

.app-job-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 5px 0;
}

.app-client {
    font-size: 0.9rem;
    color: #666;
}

.status-badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge.hired {
    background: #e8f5e9;
    color: #2e7d32;
}

.status-badge.rejected {
    background: #ffebee;
    color: #c62828;
}

.status-badge.pending {
    background: #fff3e0;
    color: #f57c00;
}

.app-description {
    color: #555;
    line-height: 1.6;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.app-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.app-meta {
    display: flex;
    gap: 10px;
}

.skill-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}

.budget-tag {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 700;
}

.applied-date {
    font-size: 0.85rem;
    color: #999;
}

.btn-primary {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 30px;
    background: #00a2ff;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background: #0090e0;
    transform: translateY(-2px);
}
</style>