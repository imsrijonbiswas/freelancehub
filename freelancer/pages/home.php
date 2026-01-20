<?php
// FreelanceHub/freelancer/pages/home.php

$freelancer_id = $_SESSION['freelancer_id'];
$skill_id = $_SESSION['freelancer_skill_id'];

$apps_query = $conn->query("
    SELECT COUNT(*) as total FROM job_applications WHERE freelancer_id = $freelancer_id
");
$total_applications = $apps_query->fetch_assoc()['total'] ?? 0;

$available_jobs_query = $conn->query("
    SELECT COUNT(*) as total FROM jobs 
    WHERE skill_id = $skill_id 
    AND status = 'open'
    AND is_hired = FALSE
    AND job_id NOT IN (
        SELECT job_id FROM job_applications WHERE freelancer_id = $freelancer_id
    )
");
$available_jobs = $available_jobs_query->fetch_assoc()['total'] ?? 0;

$score_query = $conn->prepare("
    SELECT score FROM freelancer_test_attempts 
    WHERE freelancer_email = ? AND passed = TRUE
    ORDER BY attempted_at DESC LIMIT 1
");
$score_query->bind_param("s", $_SESSION['freelancer_email']);
$score_query->execute();
$score_result = $score_query->get_result();
$test_score = $score_result->num_rows > 0 ? $score_result->fetch_assoc()['score'] : 0;

$recent_app_query = $conn->prepare("
    SELECT ja.application_id, ja.applied_at, 
           j.job_title, j.budget, j.job_description,
           c.full_name as client_name,
           s.skill_name
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.job_id
    JOIN clients c ON j.client_id = c.client_id
    JOIN skill_categories s ON j.skill_id = s.skill_id
    WHERE ja.freelancer_id = ?
    ORDER BY ja.applied_at DESC
    LIMIT 1
");
$recent_app_query->bind_param("i", $freelancer_id);
$recent_app_query->execute();
$recent_app_result = $recent_app_query->get_result();
$recent_application = $recent_app_result->num_rows > 0 ? $recent_app_result->fetch_assoc() : null;
?>

<div class="welcome-card card">
    <h2>Welcome, <?= htmlspecialchars(explode(' ', $_SESSION['freelancer_name'])[0]) ?>! 👋</h2>
    <p>Here's what's happening with your freelance work today.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e3f2fd;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#1976d2">
                <path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?= $available_jobs ?></span>
            <span class="stat-label">Available Jobs</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #e8f5e9;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#2e7d32">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?= $total_applications ?></span>
            <span class="stat-label">My Applications</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #fff3e0;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="#f57c00">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?= number_format($test_score, 0) ?>%</span>
            <span class="stat-label">Test Score</span>
        </div>
    </div>
</div>

<?php if ($recent_application): ?>
<div class="card recent-activity-card">
    <div class="section-header">
        <h3>Recent Application</h3>
        <a href="dashboard.php?page=applications" class="view-all-link">View All</a>
    </div>
    
    <div class="recent-job-card">
        <div class="job-card-header">
            <div class="job-info">
                <h4 class="job-title"><?= htmlspecialchars($recent_application['job_title']) ?></h4>
                <div class="job-meta">
                    <span class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2 1a4 4 0 0 1 3.5 5.5.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5A4 4 0 0 1 5 9h3z"/>
                        </svg>
                        <?= htmlspecialchars($recent_application['client_name']) ?>
                    </span>
                    <span class="meta-item">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                        </svg>
                        Applied <?= date('M d, Y', strtotime($recent_application['applied_at'])) ?>
                    </span>
                </div>
            </div>
            <div class="job-budget">
                $<?= number_format($recent_application['budget'], 2) ?>
            </div>
        </div>
        
        <p class="job-description">
            <?= htmlspecialchars(substr($recent_application['job_description'], 0, 150)) ?>
            <?= strlen($recent_application['job_description']) > 150 ? '...' : '' ?>
        </p>
        
        <div class="job-footer">
            <span class="skill-badge">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="currentColor" style="margin-right: 4px;">
                    <path d="M7 0L9 5h5l-4 3 1.5 5L7 10l-4.5 3L4 8 0 5h5z"/>
                </svg>
                <?= htmlspecialchars($recent_application['skill_name']) ?>
            </span>
            <!-- <span class="status-pending">Pending Review</span> -->
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h3 style="margin-bottom: 15px; color: #1a1a1a;">Quick Actions</h3>
    <div class="quick-actions">
        <a href="dashboard.php?page=jobs" class="action-btn primary">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M16 6h-1.5V4.5A2.5 2.5 0 0 0 12 2h-4a2.5 2.5 0 0 0-2.5 2.5V6H4a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2zM7.5 4.5A.5.5 0 0 1 8 4h4a.5.5 0 0 1 .5.5V6h-5V4.5z"/>
            </svg>
            Browse Jobs
        </a>
        <a href="dashboard.php?page=applications" class="action-btn secondary">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M15 2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM9 14H7v-2h2v2zm0-4H7V6h2v4zm4 4h-2V6h2v8z"/>
            </svg>
            View Applications
        </a>
        <a href="dashboard.php?page=profile" class="action-btn secondary">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 2a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM4 14a6 6 0 1 1 12 0H4z"/>
            </svg>
            Edit Profile
        </a>
    </div>
</div>

<link rel="stylesheet" href="css/freelancer_home.css">