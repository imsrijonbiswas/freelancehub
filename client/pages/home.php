<?php
// FreelanceHub/client/pages/home.php

$client_id = $_SESSION['client_id'];

// Get statistics
$total_jobs_query = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE client_id = $client_id");
$total_jobs = $total_jobs_query->fetch_assoc()['total'] ?? 0;

$open_jobs_query = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE client_id = $client_id AND status = 'open'");
$open_jobs = $open_jobs_query->fetch_assoc()['total'] ?? 0;

$hired_query = $conn->query("SELECT COUNT(DISTINCT freelancer_id) as total FROM hired_freelancers WHERE client_id = $client_id");
$total_hired = $hired_query->fetch_assoc()['total'] ?? 0;

// Get recent jobs
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

    <!-- Quick Actions -->
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

    <!-- Stats Overview -->
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

    <!-- Recent Jobs -->
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

<style>
.view-all-link {
    color: #00a2ff;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: color 0.2s ease;
}

.view-all-link:hover {
    color: #0090e0;
}

.jobs-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.job-item-home {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: transform 0.2s ease;
}

.job-item-home:hover {
    transform: translateX(4px);
}

.job-info-home {
    flex: 1;
}

.job-info-home h4 {
    font-size: 1.05rem;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 10px;
}

.job-meta-home {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.skill-badge-small {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.job-budget-small {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 700;
}

.job-status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.job-status-badge.open {
    background: #fff3e0;
    color: #f57c00;
}

.job-status-badge.closed {
    background: #e0e0e0;
    color: #666;
}

.job-desc-small {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 10px;
}

.job-footer-home {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #999;
}

.application-count {
    font-weight: 600;
    color: #00a2ff;
}

.btn-view-small {
    padding: 8px 20px;
    background: #00a2ff;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-view-small:hover {
    background: #0090e0;
    transform: translateY(-2px);
}

.btn-primary {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 24px;
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