<?php
// FreelanceHub/client/pages/freelancers.php
$client_id = $_SESSION['client_id'];
$view_freelancer_id = $_GET['id'] ?? null;

if ($view_freelancer_id) {
    $freelancer_query = $conn->prepare("
        SELECT f.*, s.skill_name,
               (SELECT COUNT(*) FROM user_reports WHERE reported_user_id = f.freelancer_id AND reported_user_type = 'freelancer') as total_reports,
               (SELECT COUNT(*) FROM hired_freelancers 
                WHERE freelancer_id = f.freelancer_id AND status = 'completed') as completed_jobs
        FROM freelancers f
        LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
        WHERE f.freelancer_id = ?
    ");
    $freelancer_query->bind_param("i", $view_freelancer_id);
    $freelancer_query->execute();
    $freelancer = $freelancer_query->get_result()->fetch_assoc();
    
    if (!$freelancer) {
        header('Location: dashboard.php?page=freelancers&error=Freelancer not found');
        exit();
    }
    
    $worked_together_query = $conn->prepare("
        SELECT hf.hire_id, hf.job_id, j.job_title 
        FROM hired_freelancers hf
        JOIN jobs j ON hf.job_id = j.job_id
        WHERE hf.client_id = ? AND hf.freelancer_id = ?
        ORDER BY hf.hired_at DESC
    ");
    $worked_together_query->bind_param("ii", $client_id, $view_freelancer_id);
    $worked_together_query->execute();
    $worked_jobs = $worked_together_query->get_result();
} else {
    $freelancers_query = $conn->query("
        SELECT f.*, s.skill_name,
               (SELECT COUNT(*) FROM user_reports WHERE reported_user_id = f.freelancer_id AND reported_user_type = 'freelancer') as total_reports,
               (SELECT COUNT(*) FROM hired_freelancers 
                WHERE freelancer_id = f.freelancer_id AND status = 'completed') as completed_jobs,
               (SELECT score FROM freelancer_test_attempts 
                WHERE freelancer_email = f.email AND passed = TRUE 
                ORDER BY attempted_at DESC LIMIT 1) as test_score
        FROM freelancers f
        LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
        WHERE f.is_verified = TRUE
        ORDER BY test_score DESC, completed_jobs DESC
    ");
}
?>

<link rel="stylesheet" href="css/freelancers.css">

<?php if ($view_freelancer_id && $freelancer): ?>
    <div class="freelancer-profile-container">
        <div class="profile-header">
            <a href="dashboard.php?page=freelancers" class="btn-back">← Back to Freelancers</a>
        </div>
        
        <div class="profile-card">
            <div class="profile-avatar-large">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            
            <h2><?= htmlspecialchars($freelancer['full_name']) ?></h2>
            <p class="profile-skill"><?= htmlspecialchars($freelancer['skill_name']) ?></p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= $freelancer['total_reports'] ?></span>
                    <span class="stat-label">Reports</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $freelancer['completed_jobs'] ?></span>
                    <span class="stat-label">Jobs Done</span>
                </div>
            </div>
            
            <div class="profile-bio">
                <h3>About</h3>
                <p><?= nl2br(htmlspecialchars($freelancer['bio'] ?? 'No bio provided')) ?></p>
            </div>
            
            <?php if ($worked_jobs->num_rows > 0): ?>
                <div class="worked-jobs-section">
                    <h3>Jobs You Hired Them For</h3>
                    <div class="jobs-list">
                        <?php while ($job = $worked_jobs->fetch_assoc()): ?>
                            <div class="job-item">
                                <h4><?= htmlspecialchars($job['job_title']) ?></h4>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
<?php else: ?>
    <div class="freelancers-container">
        <div class="page-header">
            <h1>Browse Freelancers</h1>
            <p class="subtitle">Find verified freelancers with high test scores</p>
        </div>
        
        <div class="freelancers-grid">
            <?php if ($freelancers_query && $freelancers_query->num_rows > 0): ?>
                <?php while ($freelancer = $freelancers_query->fetch_assoc()): ?>
                    <div class="freelancer-card">
                        <div class="freelancer-avatar">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        
                        <h3><?= htmlspecialchars($freelancer['full_name']) ?></h3>
                        <p class="freelancer-skill"><?= htmlspecialchars($freelancer['skill_name']) ?></p>
                        
                        <div class="freelancer-stats">
                            <span class="reports-badge">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <?= $freelancer['total_reports'] ?> Reports
                            </span>
                            <span class="test-badge">Test: <?= number_format($freelancer['test_score'], 0) ?>%</span>
                        </div>
                        
                        <a href="dashboard.php?page=freelancers&id=<?= $freelancer['freelancer_id'] ?>" 
                           class="btn-view-profile">
                            View Profile
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No verified freelancers available yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>