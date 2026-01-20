<?php
// FreelanceHub/client/pages/jobs.php

$client_id = $_SESSION['client_id'];

$jobs_query = $conn->query("
    SELECT j.*, s.skill_name,
           (SELECT COUNT(*) FROM job_applications WHERE job_id = j.job_id) as application_count,
           f.full_name as hired_freelancer_name,
           hf.hired_at
    FROM jobs j
    LEFT JOIN skill_categories s ON j.skill_id = s.skill_id
    LEFT JOIN hired_freelancers hf ON j.job_id = hf.job_id AND hf.status = 'active'
    LEFT JOIN freelancers f ON j.hired_freelancer_id = f.freelancer_id
    WHERE j.client_id = $client_id
    ORDER BY j.created_at DESC
");

$message = $_GET['msg'] ?? '';
?>

<link rel="stylesheet" href="css/my_jobs.css">

<?php if ($message == 'closed'): ?>
    <div class="success-message">Job closed successfully!</div>
<?php elseif ($message == 'hired'): ?>
    <div class="success-message">Freelancer hired successfully!</div>
<?php elseif ($message == 'already_hired'): ?>
    <div class="error-message">You have already hired a freelancer for this job!</div>
<?php endif; ?>

<div class="my-jobs-container">
    <div class="page-header">
        <h1>My Jobs</h1>
        <a href="dashboard.php?page=post-job" class="btn-new-job">+ Post New Job</a>
    </div>

    <?php if ($jobs_query && $jobs_query->num_rows > 0): ?>
        <div class="jobs-list">
            <?php while ($job = $jobs_query->fetch_assoc()): ?>
                <div class="job-card-detailed">
                    <div class="job-card-header">
                        <div>
                            <h3><?= htmlspecialchars($job['job_title']) ?></h3>
                            <div class="job-meta-tags">
                                <span class="skill-tag"><?= htmlspecialchars($job['skill_name']) ?></span>
                                <span class="budget-tag">$<?= number_format($job['budget'], 2) ?></span>
                                <span class="status-tag <?= $job['status'] ?>">
                                    <?= ucfirst($job['status']) ?>
                                </span>
                                <?php if ($job['is_hired']): ?>
                                    <span class="hired-tag">Hired</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="job-date"><?= date('M d, Y', strtotime($job['created_at'])) ?></span>
                    </div>
                    
                    <p class="job-description"><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>
                    
    <?php if ($job['is_hired'] && $job['hired_freelancer_name']): ?>
        <div class="hired-freelancer-info">
            <div class="hired-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"                 stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            <span>Hired: <?= htmlspecialchars($job['hired_freelancer_name']) ?></span>
        </div>
        <span class="hired-date">on <?= date('M d, Y', strtotime($job['hired_at'])) ?></span>
            <a href="dashboard.php?page=freelancers&id=<?= $job['hired_freelancer_id'] ?>" 
                class="btn-view-profile">View Profile
            </a>
                <button onclick="openReportModal(<?= $job['hired_freelancer_id'] ?>, <?= $job['job_id'] ?>)" 
                class="btn-report-hired">
            Report Issue
            </button>
        </div>
    <?php endif; ?>
                    
                    <div class="job-card-footer">
                        <span class="applications-count">
                            <?= $job['application_count'] ?> applications received
                        </span>
                        <div class="job-actions">
                            <?php if (!$job['is_hired']): ?>
                                <a href="dashboard.php?page=jobs&view_applications=<?= $job['job_id'] ?>" 
                                   class="btn-view-apps">
                                    View Applications
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($job['status'] == 'open' && !$job['is_hired']): ?>
                                <a href="actions/close_job.php?id=<?= $job['job_id'] ?>" 
                                   class="btn-close-job"
                                   onclick="return confirm('Are you sure you want to close this job?')">
                                    Close Job
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php
                $viewing_job_id = $_GET['view_applications'] ?? 0;
                if ($viewing_job_id == $job['job_id'] && !$job['is_hired']):
                    $apps_query = $conn->prepare("
                        SELECT ja.*, f.full_name, f.email, f.freelancer_id,
                               (SELECT score FROM freelancer_test_attempts 
                                WHERE freelancer_email = f.email AND passed = TRUE 
                                ORDER BY attempted_at DESC LIMIT 1) as test_score
                        FROM job_applications ja
                        JOIN freelancers f ON ja.freelancer_id = f.freelancer_id
                        WHERE ja.job_id = ?
                        ORDER BY ja.applied_at DESC
                    ");
                    $apps_query->bind_param("i", $job['job_id']);
                    $apps_query->execute();
                    $applications = $apps_query->get_result();
                ?>
                    <div class="applications-section">
                        <h4>Applications for "<?= htmlspecialchars($job['job_title']) ?>"</h4>
                        <?php if ($applications->num_rows > 0): ?>
                            <div class="applications-list">
                                <?php while ($app = $applications->fetch_assoc()): ?>
                                    <div class="application-item">
                                        <div class="applicant-info">
                                            <h5><?= htmlspecialchars($app['full_name']) ?></h5>
                                            <p class="applicant-email"><?= htmlspecialchars($app['email']) ?></p>
                                            <span class="test-score">Test Score: <?= number_format($app['test_score'], 0) ?>%</span>
                                        </div>
                                        <div class="application-actions">
                                            <span class="applied-date">Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></span>
                                            <a href="actions/hire_freelancer.php?job_id=<?= $job['job_id'] ?>&freelancer_id=<?= $app['freelancer_id'] ?>" 
                                               class="btn-hire"
                                               onclick="return confirm('Hire this freelancer? You can only hire one freelancer per job.')">
                                                Hire
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="no-applications">No applications yet</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <p>No jobs posted yet</p>
            <a href="dashboard.php?page=post-job" class="btn-primary">Post Your First Job</a>
        </div>
    <?php endif; ?>

<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReportModal()">&times;</span>
        <h2>Report Fraudulent Activity</h2>
        <form action="actions/report_freelancer.php" method="POST">
            <input type="hidden" name="freelancer_id" id="report_freelancer_id">
            <input type="hidden" name="job_id" id="report_job_id">
            
            <div class="form-group">
                <label>Reason *</label>
                <select name="report_reason" required>
                    <option value="">Select a reason</option>
                    <option value="Poor Work Quality">Poor Work Quality</option>
                    <option value="Missed Deadline">Missed Deadline</option>
                    <option value="Unprofessional Behavior">Unprofessional Behavior</option>
                    <option value="Incomplete Work">Incomplete Work</option>
                    <option value="Scam/Fraud">Scam/Fraud</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Additional Details *</label>
                <textarea name="report_message" rows="4" required 
                          placeholder="Please provide details about the issue..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" onclick="closeReportModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit-report">Submit Report</button>
            </div>
        </form>
    </div>
</div>

<script>
function openReportModal(freelancerId, jobId) {
    document.getElementById('report_freelancer_id').value = freelancerId;
    document.getElementById('report_job_id').value = jobId;
    document.getElementById('reportModal').style.display = 'block';
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('reportModal');
    if (event.target == modal) {
        closeReportModal();
    }
}
</script>
</div>