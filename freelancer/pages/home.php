<?php
// FreelanceHub/freelancer/pages/home.php

// Get freelancer statistics
$freelancer_id = $_SESSION['freelancer_id'];

// Count total applications
$apps_query = $conn->query("
    SELECT COUNT(*) as total FROM job_applications WHERE freelancer_id = $freelancer_id
");
$total_applications = $apps_query->fetch_assoc()['total'] ?? 0;

// Count available jobs for this skill
$skill_id = $_SESSION['freelancer_skill_id'];
$jobs_query = $conn->query("
    SELECT COUNT(*) as total FROM jobs WHERE skill_id = $skill_id
");
$available_jobs = $jobs_query->fetch_assoc()['total'] ?? 0;

// Get test score
$score_query = $conn->prepare("
    SELECT score FROM freelancer_test_attempts 
    WHERE freelancer_email = ? AND passed = TRUE
    ORDER BY attempted_at DESC LIMIT 1
");
$score_query->bind_param("s", $_SESSION['freelancer_email']);
$score_query->execute();
$score_result = $score_query->get_result();
$test_score = $score_result->num_rows > 0 ? $score_result->fetch_assoc()['score'] : 0;
?>

<div class="welcome-card card">
    <h2>Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['freelancer_name'])[0]) ?>! 👋</h2>
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

<style>
.welcome-card h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.welcome-card p {
    color: #666;
    font-size: 0.95rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a1a1a;
}

.stat-label {
    font-size: 0.85rem;
    color: #666;
}

.quick-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.action-btn.primary {
    background: #00a2ff;
    color: #fff;
}

.action-btn.primary:hover {
    background: #0090e0;
    transform: translateY(-2px);
}

.action-btn.secondary {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #e0e0e0;
}

.action-btn.secondary:hover {
    background: #e0e0e0;
}
</style>