<?php
// FreelanceHub/client/pages/dashboard.php
$pageCSS = 'client/css/dashboard_content.css';

$ongoingQuery = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE client_id = $clientId AND status = 'ongoing'");
$ongoing = $ongoingQuery->fetch_assoc()['count'];

$pendingQuery = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE client_id = $clientId AND status = 'pending'");
$pending = $pendingQuery->fetch_assoc()['count'];

$completedQuery = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE client_id = $clientId AND status = 'completed'");
$completed = $completedQuery->fetch_assoc()['count'];

$ongoingJobsQuery = $conn->query("
    SELECT j.job_id, j.title, j.status, f.name as freelancer_name, 
           j.created_at, a.status as application_status
    FROM jobs j
    LEFT JOIN applications a ON j.job_id = a.job_id AND a.status = 'accepted'
    LEFT JOIN freelancers f ON a.freelancer_id = f.freelancer_id
    WHERE j.client_id = $clientId AND j.status = 'ongoing'
    ORDER BY j.created_at DESC
    LIMIT 3
");

$proposalsQuery = $conn->query("
    SELECT j.job_id, j.title, a.proposal, f.name as freelancer_name
    FROM jobs j
    JOIN applications a ON j.job_id = a.job_id
    JOIN freelancers f ON a.freelancer_id = f.freelancer_id
    WHERE j.client_id = $clientId AND a.status = 'pending'
    ORDER BY a.applied_at DESC
    LIMIT 3
");

$savedQuery = $conn->query("
    SELECT f.freelancer_id, f.name, f.skills, fr.avg_rating
    FROM saved_freelancers sf
    JOIN freelancers f ON sf.freelancer_id = f.freelancer_id
    LEFT JOIN (
        SELECT freelancer_id, AVG(rating) as avg_rating
        FROM feedback
        GROUP BY freelancer_id
    ) fr ON f.freelancer_id = fr.freelancer_id
    WHERE sf.client_id = $clientId
    ORDER BY sf.saved_at DESC
    LIMIT 3
");

$messagesQuery = $conn->query("
    SELECT m.message_id, m.message, m.sent_at, f.name as freelancer_name
    FROM messages m
    JOIN freelancers f ON m.sender_id = f.freelancer_id
    WHERE m.receiver_id = $clientId AND m.sender_role = 'freelancer'
    ORDER BY m.sent_at DESC
    LIMIT 3
");

$balanceQuery = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE client_id = $clientId");
$totalSpent = $balanceQuery->fetch_assoc()['total'];

$lastPaymentQuery = $conn->query("SELECT amount, payment_date FROM payments WHERE client_id = $clientId ORDER BY payment_date DESC LIMIT 1");
$lastPayment = $lastPaymentQuery->fetch_assoc();

$recommendedQuery = $conn->query("
    SELECT j.job_id, j.title, j.description, j.required_skills, j.posted_date, c.name as category
    FROM jobs j
    LEFT JOIN categories c ON j.category_id = c.category_id
    WHERE j.status = 'open' AND j.client_id != $clientId
    ORDER BY j.posted_date DESC
    LIMIT 3
");
?>

<link rel="stylesheet" href="css/dashboard_content.css">

<div class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Welcome <?= htmlspecialchars($client['name']) ?></h1>
            <p class="subtitle">Here's Your Current Activity Summary</p>
        </div>
        <a href="?page=post-job" class="btn-log-out">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Log Out
        </a>
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
                <h3>Post a Job</h3>
                <p>Create a new job listing and receive proposals instantly</p>
            </div>
            <a href="?page=post-job" class="action-btn">Post Now</a>
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
                <h3>View Top Scored Freelancers</h3>
                <p>Freelancers with highest test scores</p>
            </div>
            <a href="?page=freelancers" class="action-btn">Browse</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Ongoing Jobs</span>
                <div class="stat-icon ongoing">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $ongoing ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Pending Proposals</span>
                <div class="stat-icon pending">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $pending ?></div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Saved Freelancers</span>
                <div class="stat-icon saved">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?= $savedQuery->num_rows ?></div>
        </div>
    </div>

    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h3>Ongoing Job</h3>
            </div>
            <div class="card-body">
                <?php if ($ongoingJobsQuery->num_rows > 0): ?>
                    <?php while ($job = $ongoingJobsQuery->fetch_assoc()): ?>
                        <div class="job-item">
                            <div class="job-info">
                                <h4><?= htmlspecialchars($job['title']) ?></h4>
                                <div class="job-meta">
                                    <span class="freelancer-name">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <?= htmlspecialchars($job['freelancer_name'] ?? 'No freelancer assigned') ?>
                                    </span>
                                    <span class="job-date"><?= date('M d, Y', strtotime($job['created_at'])) ?></span>
                                </div>
                                <span class="badge completed">Completed</span>
                            </div>
                            <a href="?page=jobs" class="btn-view">View</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">No ongoing jobs at the moment</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>Pending Proposals</h3>
            </div>
            <div class="card-body">
                <?php if ($proposalsQuery->num_rows > 0): ?>
                    <?php while ($proposal = $proposalsQuery->fetch_assoc()): ?>
                        <div class="proposal-item">
                            <div class="proposal-info">
                                <h4><?= htmlspecialchars($proposal['title']) ?></h4>
                                <p class="proposal-text">Proposal: <?= htmlspecialchars(substr($proposal['proposal'], 0, 50)) ?>...</p>
                                <span class="proposal-meta">Waiting for review</span>
                            </div>
                            <a href="?page=jobs&job_id=<?= $proposal['job_id'] ?>" class="btn-view-proposals">View Proposals</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">No pending proposals</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>Saved Freelancers</h3>
            </div>
            <div class="card-body">
                <?php if ($savedQuery->num_rows > 0): ?>
                    <?php while ($freelancer = $savedQuery->fetch_assoc()): ?>
                        <div class="freelancer-item">
                            <div class="freelancer-avatar">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="freelancer-info">
                                <h4><?= htmlspecialchars($freelancer['name']) ?></h4>
                                <p class="freelancer-skills"><?= htmlspecialchars($freelancer['skills']) ?></p>
                                <div class="rating">
                                    <?php
                                    $rating = $freelancer['avg_rating'] ?? 0;
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $rating ? '⭐' : '☆';
                                    }
                                    ?>
                                    <span><?= number_format($rating, 1) ?>/5</span>
                                </div>
                            </div>
                            <a href="?page=freelancers&id=<?= $freelancer['freelancer_id'] ?>" class="btn-view-profile">View Profile</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">No saved freelancers yet</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>Recommended Jobs</h3>
            </div>
            <div class="card-body">
                <?php if ($recommendedQuery->num_rows > 0): ?>
                    <?php while ($rec = $recommendedQuery->fetch_assoc()): ?>
                        <div class="recommended-job">
                            <h4><?= htmlspecialchars($rec['title']) ?></h4>
                            <p class="job-category"><?= htmlspecialchars($rec['category'] ?? 'General') ?></p>
                            <div class="job-skills">
                                <?php
                                $skills = explode(',', $rec['required_skills']);
                                foreach (array_slice($skills, 0, 3) as $skill):
                                ?>
                                    <span class="skill-tag"><?= htmlspecialchars(trim($skill)) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <a href="?page=jobs&view=<?= $rec['job_id'] ?>" class="btn-view">View</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">No recommendations available</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>Messages</h3>
            </div>
            <div class="card-body">
                <?php if ($messagesQuery->num_rows > 0): ?>
                    <?php while ($msg = $messagesQuery->fetch_assoc()): ?>
                        <div class="message-item">
                            <div class="message-avatar">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="message-content">
                                <h4><?= htmlspecialchars($msg['freelancer_name']) ?></h4>
                                <p><?= htmlspecialchars(substr($msg['message'], 0, 50)) ?>...</p>
                                <span class="message-time"><?= date('h:i A', strtotime($msg['sent_at'])) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <a href="?page=messages" class="btn-open-messages">Open Messages</a>
                <?php else: ?>
                    <p class="empty-state">No messages yet</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h3>Payment Summary</h3>
            </div>
            <div class="card-body payment-summary">
                <div class="payment-stat">
                    <span class="payment-label">Current Balance:</span>
                    <span class="payment-value">${= number_format($totalSpent, 2) ?></span>
                </div>
                <?php if ($lastPayment): ?>
                    <div class="payment-stat">
                        <span class="payment-label">Last Transaction:</span>
                        <span class="payment-value">$<?= number_format($lastPayment['amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <a href="?page=payments" class="btn-view-payments">View Payments</a>
            </div>
        </div>
    </div>
</div>