<?php
// FreelanceHub/client/pages/home.php

$client_id = $_SESSION['client_id'];

$total_jobs_query = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE client_id = $client_id");
$total_jobs = $total_jobs_query->fetch_assoc()['total'] ?? 0;

$open_jobs_query = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE client_id = $client_id AND status = 'open'");
$open_jobs = $open_jobs_query->fetch_assoc()['total'] ?? 0;

$hired_query = $conn->query("SELECT COUNT(DISTINCT freelancer_id) as total FROM hired_freelancers WHERE client_id = $client_id");
$total_hired = $hired_query->fetch_assoc()['total'] ?? 0;

$recent_jobs_query = $conn->query("
    SELECT j.*, s.skill_name,
           (SELECT COUNT(*) FROM job_applications WHERE job_id = j.job_id) as application_count
    FROM jobs j
    LEFT JOIN skill_categories s ON j.skill_id = s.skill_id
    WHERE j.client_id = $client_id
    ORDER BY j.created_at DESC
    LIMIT 5
");
?>

<link rel="stylesheet" href="css/dashboard_content.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['client_name'])[0]) ?>! 👋</h1>
            <p class="subtitle">Here's what's happening with your jobs today</p>
        </div>
    </div>

    <div class="action-cards">
        <div class="action-card primary">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </div>
            <div class="action-content">
                <h3>Post a New Job</h3>
                <p>Create a job listing and start receiving applications</p>
            </div>
            <a href="dashboard.php?page=post-job" class="action-btn">Post Now</a>
        </div>

        <div class="action-card secondary">
            <div class="action-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="action-content">
                <h3>Find Top Freelancers</h3>
                <p>Browse verified freelancers by skill category</p>
            </div>
            <a href="dashboard.php?page=freelancers" class="action-btn">Browse</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Jobs Posted</span>
                <div class="stat-icon ongoing">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $total_jobs ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Open Jobs</span>
                <div class="stat-icon pending">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $open_jobs ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Hired Freelancers</span>
                <div class="stat-icon saved">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $total_hired ?></div>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3>Recent Jobs</h3>
            <a href="dashboard.php?page=jobs" class="view-all-link">View All →</a>
        </div>
        <div class="card-body">
            <?php if ($recent_jobs_query && $recent_jobs_query->num_rows > 0): ?>
                <div class="jobs-list">
                    <?php while ($job = $recent_jobs_query->fetch_assoc()): ?>
                        <div class="job-item-home">
                            <div class="job-info-home">
                                <h4><?= htmlspecialchars($job['job_title']) ?></h4>
                                <div class="job-meta-home">
                                    <span class="skill-badge-small"><?= htmlspecialchars($job['skill_name']) ?></span>
                                    <span class="job-budget-small">$<?= number_format($job['budget'], 2) ?></span>
                                    <span class="job-status-badge <?= $job['status'] ?>">
                                        <?= ucfirst($job['status']) ?>
                                    </span>
                                </div>
                                <p class="job-desc-small"><?= htmlspecialchars(substr($job['job_description'], 0, 100)) ?>...</p>
                                <div class="job-footer-home">
                                    <span class="application-count"><?= $job['application_count'] ?> applications</span>
                                    <span class="job-date-small"><?= date('M d, Y', strtotime($job['created_at'])) ?></span>
                                </div>
                            </div>
                            <a href="dashboard.php?page=jobs&view=<?= $job['job_id'] ?>" class="btn-view-small">View</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No jobs posted yet. Start by posting your first job!</p>
                    <a href="dashboard.php?page=post-job" class="btn-primary">Post a Job</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="css/client_home.css">