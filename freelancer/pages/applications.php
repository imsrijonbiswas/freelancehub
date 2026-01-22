<?php
// FreelanceHub/freelancer/pages/applications.php

$freelancer_id = $_SESSION['freelancer_id'];

$apps_query = $conn->query("
    SELECT 
        ja.application_id,
        ja.applied_at,
        ja.job_id,
        j.job_title,
        j.job_description,
        j.budget,
        j.status as job_status,
        c.client_id,
        c.full_name as client_name,
        s.skill_name,
        CASE 
            WHEN hf.hire_id IS NOT NULL THEN 'hired'
            WHEN j.status = 'closed' THEN 'rejected'
            ELSE 'pending'
        END as application_status,
        (SELECT COUNT(*) FROM user_reports 
         WHERE job_id = ja.job_id 
         AND reported_user_id = c.client_id 
         AND reporter_id = $freelancer_id 
         AND reporter_type = 'freelancer') as already_reported
    FROM job_applications ja
    JOIN jobs j ON ja.job_id = j.job_id
    JOIN clients c ON j.client_id = c.client_id
    JOIN skill_categories s ON j.skill_id = s.skill_id
    LEFT JOIN hired_freelancers hf ON ja.job_id = hf.job_id AND ja.freelancer_id = hf.freelancer_id
    WHERE ja.freelancer_id = $freelancer_id
    ORDER BY ja.applied_at DESC
");

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($message == 'reported'): ?>
    <div class="success-message">Client reported successfully! Admin will review it.</div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="applications-container">
    <?php if ($apps_query && $apps_query->num_rows > 0): ?>
        <div class="applications-list">
            <?php while ($app = $apps_query->fetch_assoc()): ?>
                <div class="application-card status-<?= $app['application_status'] ?>">
                    <div class="app-header">
                        <div>
                            <h3 class="app-job-title"><?= htmlspecialchars($app['job_title']) ?></h3>
                            <span class="app-client">
                                by <a href="dashboard.php?page=client_profile&id=<?= $app['client_id'] ?>" class="client-name-link"><?= htmlspecialchars($app['client_name']) ?></a>
                            </span>
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
                        <div class="app-actions">
                            <span class="applied-date">Applied: <?= date('M d, Y', strtotime($app['applied_at'])) ?></span>
                            <?php if ($app['application_status'] == 'hired'): ?>
                                <?php if ($app['already_reported'] == 0): ?>
                                    <button onclick="openReportModal(<?= $app['client_id'] ?>, <?= $app['job_id'] ?>)" class="btn-report-client">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        Report Client
                                    </button>
                                <?php else: ?>
                                    <span class="already-reported-badge">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16" stroke="white" stroke-width="2"></line>
                                        </svg>
                                        Reported
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
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

<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReportModal()">&times;</span>
        <h2>Report Fraudulent Activity</h2>
        <form action="actions/report_client.php" method="POST">
            <input type="hidden" name="client_id" id="report_client_id">
            <input type="hidden" name="job_id" id="report_job_id">
            
            <div class="form-group">
                <label>Reason *</label>
                <select name="report_reason" required>
                    <option value="">Select a reason</option>
                    <option value="Non-Payment">Non-Payment</option>
                    <option value="Unclear Requirements">Unclear Requirements</option>
                    <option value="Unprofessional Behavior">Unprofessional Behavior</option>
                    <option value="Scope Creep">Scope Creep</option>
                    <option value="Harassment">Harassment</option>
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
function openReportModal(clientId, jobId) {
    document.getElementById('report_client_id').value = clientId;
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

<link rel="stylesheet" href="css/applications.css">