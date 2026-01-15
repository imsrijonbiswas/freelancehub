<?php
// FreelanceHub/client/pages/jobs.php

$status = $_GET['status'] ?? 'all';
$jobId = $_GET['job_id'] ?? null;

// Build query based on status
$statusCondition = $status !== 'all' ? "AND j.status = '$status'" : "";

$jobsQuery = $conn->query("
    SELECT j.*, c.name as category_name, 
           COUNT(DISTINCT a.application_id) as proposal_count,
           (SELECT COUNT(*) FROM applications WHERE job_id = j.job_id AND status = 'accepted') as hired_count
    FROM jobs j
    LEFT JOIN categories c ON j.category_id = c.category_id
    LEFT JOIN applications a ON j.job_id = a.job_id
    WHERE j.client_id = $clientId $statusCondition
    GROUP BY j.job_id
    ORDER BY j.posted_date DESC
");

// If viewing specific job proposals
$proposals = [];
if ($jobId) {
    $proposalsQuery = $conn->query("
        SELECT a.*, f.name, f.skills, f.email,
               COALESCE(AVG(fb.rating), 0) as avg_rating,
               COUNT(fb.feedback_id) as total_reviews
        FROM applications a
        JOIN freelancers f ON a.freelancer_id = f.freelancer_id
        LEFT JOIN feedback fb ON f.freelancer_id = fb.freelancer_id
        WHERE a.job_id = $jobId
        GROUP BY a.application_id
        ORDER BY a.applied_at DESC
    ");
    $proposals = $proposalsQuery->fetch_all(MYSQLI_ASSOC);
}
?>

<link rel="stylesheet" href="css/jobs.css">

<div class="jobs-container">
    <div class="jobs-header">
        <h1>My Jobs</h1>
        <a href="?page=post-job" class="btn-post-new">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Post New Job
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="?page=jobs&status=all" class="tab <?= $status === 'all' ? 'active' : '' ?>">All Jobs</a>
        <a href="?page=jobs&status=open" class="tab <?= $status === 'open' ? 'active' : '' ?>">Open</a>
        <a href="?page=jobs&status=ongoing" class="tab <?= $status === 'ongoing' ? 'active' : '' ?>">Ongoing</a>
        <a href="?page=jobs&status=completed" class="tab <?= $status === 'completed' ? 'active' : '' ?>">Completed</a>
        <a href="?page=jobs&status=closed" class="tab <?= $status === 'closed' ? 'active' : '' ?>">Closed</a>
    </div>

    <?php if ($jobId && !empty($proposals)): ?>
        <!-- Proposals View -->
        <div class="proposals-section">
            <div class="section-header">
                <h2>Proposals for Job</h2>
                <a href="?page=jobs" class="btn-back">← Back to Jobs</a>
            </div>

            <div class="proposals-list">
                <?php foreach ($proposals as $prop): ?>
                    <div class="proposal-card">
                        <div class="proposal-header">
                            <div class="freelancer-info">
                                <div class="freelancer-avatar">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h3><?= htmlspecialchars($prop['name']) ?></h3>
                                    <div class="rating">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $prop['avg_rating'] ? '⭐' : '☆';
                                        }
                                        ?>
                                        <span><?= number_format($prop['avg_rating'], 1) ?> (<?= $prop['total_reviews'] ?> reviews)</span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($prop['status'] === 'pending'): ?>
                                <span class="status-badge pending">Pending</span>
                            <?php elseif ($prop['status'] === 'accepted'): ?>
                                <span class="status-badge accepted">Hired</span>
                            <?php else: ?>
                                <span class="status-badge rejected">Rejected</span>
                            <?php endif; ?>
                        </div>

                        <div class="proposal-body">
                            <p class="proposal-text"><?= nl2br(htmlspecialchars($prop['proposal'])) ?></p>
                            <div class="freelancer-skills">
                                <?php
                                $skills = explode(',', $prop['skills']);
                                foreach ($skills as $skill):
                                ?>
                                    <span class="skill-tag"><?= htmlspecialchars(trim($skill)) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php if ($prop['status'] === 'pending'): ?>
                            <div class="proposal-actions">
                                <form action="process_hire_freelancer.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="application_id" value="<?= $prop['application_id'] ?>">
                                    <input type="hidden" name="job_id" value="<?= $jobId ?>">
                                    <button type="submit" name="action" value="accept" class="btn-accept">Hire</button>
                                    <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- Jobs List View -->
        <div class="jobs-grid">
            <?php if ($jobsQuery->num_rows > 0): ?>
                <?php while ($job = $jobsQuery->fetch_assoc()): ?>
                    <div class="job-card">
                        <div class="job-card-header">
                            <h3><?= htmlspecialchars($job['title']) ?></h3>
                            <span class="job-status <?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span>
                        </div>

                        <p class="job-description"><?= htmlspecialchars(substr($job['description'], 0, 150)) ?>...</p>

                        <div class="job-meta">
                            <div class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <?= date('M d, Y', strtotime($job['posted_date'])) ?>
                            </div>
                            <div class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                                <?= htmlspecialchars($job['category_name'] ?? 'Uncategorized') ?>
                            </div>
                        </div>

                        <div class="job-footer">
                            <div class="proposals-count">
                                <strong><?= $job['proposal_count'] ?></strong> Proposals
                                <?php if ($job['hired_count'] > 0): ?>
                                    | <strong><?= $job['hired_count'] ?></strong> Hired
                                <?php endif; ?>
                            </div>
                            <a href="?page=jobs&job_id=<?= $job['job_id'] ?>" class="btn-view-proposals">View Proposals</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <h3>No jobs found</h3>
                    <p>Start by posting your first job</p>
                    <a href="?page=post-job" class="btn-post-job">Post a Job</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>