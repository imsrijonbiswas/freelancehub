<?php
// FreelanceHub/freelancer/pages/jobs.php

$freelancer_id = $_SESSION['freelancer_id'];
$skill_id = $_SESSION['freelancer_skill_id'];

$jobs_query = $conn->query("
    SELECT j.*, c.full_name as client_name, s.skill_name
    FROM jobs j
    JOIN clients c ON j.client_id = c.client_id
    JOIN skill_categories s ON j.skill_id = s.skill_id
    WHERE j.skill_id = $skill_id 
    AND j.status = 'open'
    AND j.job_id NOT IN (
        SELECT job_id FROM job_applications WHERE freelancer_id = $freelancer_id
    )
    ORDER BY j.created_at DESC
");

$message = $_GET['msg'] ?? '';
?>

<?php if ($message == 'applied'): ?>
    <div class="success-message">Application submitted successfully!</div>
<?php endif; ?>

<div class="jobs-container">
    <?php if ($jobs_query && $jobs_query->num_rows > 0): ?>
        <div class="jobs-grid">
            <?php while ($job = $jobs_query->fetch_assoc()): ?>
                <div class="job-card">
                    <div class="job-header">
                        <h3 class="job-title"><?= htmlspecialchars($job['job_title']) ?></h3>
                        <span class="job-budget">$<?= number_format($job['budget'], 2) ?></span>
                    </div>
                    
                    <div class="job-meta">
                        <span class="client-name">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2 1a4 4 0 0 1 3.5 5.5.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5A4 4 0 0 1 5 9h3z"/>
                            </svg>
                            <?= htmlspecialchars($job['client_name']) ?>
                        </span>
                        <span class="job-date">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M11 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2H5v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2h6V2z"/>
                            </svg>
                            <?= date('M d, Y', strtotime($job['created_at'])) ?>
                        </span>
                    </div>
                    
                    <p class="job-description"><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>
                    
                    <div class="job-footer">
                        <span class="skill-badge"><?= htmlspecialchars($job['skill_name']) ?></span>
                        <a href="actions/apply_job.php?job_id=<?= $job['job_id'] ?>" 
                           class="btn-apply"
                           onclick="return confirm('Are you sure you want to apply for this job?')">
                            Apply Now
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
            <p>No jobs available at the moment. Check back later!</p>
        </div>
    <?php endif; ?>
</div>

<style>
.jobs-container {
    padding: 20px 0;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
}

.job-card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.job-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.job-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
}

.job-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    flex: 1;
}

.job-budget {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.95rem;
}

.job-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #666;
}

.job-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.job-meta svg {
    color: #00a2ff;
}

.job-description {
    color: #555;
    line-height: 1.6;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.job-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.skill-badge {
    background: #e3f2fd;
    color: #1976d2;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-apply {
    background: #00a2ff;
    color: #fff;
    padding: 10px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}

.btn-apply:hover {
    background: #0090e0;
    transform: translateX(2px);
}

@media (max-width: 768px) {
    .jobs-grid {
        grid-template-columns: 1fr;
    }
}
</style>