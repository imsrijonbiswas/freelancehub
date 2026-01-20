<?php
// FreelanceHub/freelancer/pages/client_profile.php
$freelancer_id = $_SESSION['freelancer_id'];
$client_id = $_GET['id'] ?? 0;

$client_query = $conn->prepare("
    SELECT c.*,
           (SELECT COUNT(*) FROM jobs WHERE client_id = c.client_id) as total_jobs_posted,
           (SELECT COUNT(*) FROM user_reports WHERE reported_user_id = c.client_id AND reported_user_type = 'client') as total_reports
    FROM clients c
    WHERE c.client_id = ?
");
$client_query->bind_param("i", $client_id);
$client_query->execute();
$client = $client_query->get_result()->fetch_assoc();

if (!$client) {
    header('Location: dashboard.php?page=applications&error=Client not found');
    exit();
}

$jobs_query = $conn->prepare("
    SELECT j.*, hf.hired_at, hf.status, hf.job_id
    FROM hired_freelancers hf
    JOIN jobs j ON hf.job_id = j.job_id
    WHERE hf.client_id = ? AND hf.freelancer_id = ?
    ORDER BY hf.hired_at DESC
");
$jobs_query->bind_param("ii", $client_id, $freelancer_id);
$jobs_query->execute();
$worked_jobs = $jobs_query->get_result();

$report_check = $conn->prepare("
    SELECT report_id FROM user_reports 
    WHERE reported_user_id = ? AND reported_user_type = 'client' 
    AND reporter_id = ? AND reporter_type = 'freelancer'
");
$report_check->bind_param("ii", $client_id, $freelancer_id);
$report_check->execute();
$already_reported = $report_check->get_result()->num_rows > 0;

$worked_together = $worked_jobs->num_rows > 0;

$message = $_GET['msg'] ?? '';
?>

<link rel="stylesheet" href="css/client_profile.css">

<?php if ($message == 'reported'): ?>
    <div class="success-message">Client reported successfully! Admin will review it.</div>
<?php endif; ?>

<div class="client-profile-container">
    <div class="profile-header">
        <a href="dashboard.php?page=applications" class="btn-back">← Back to Applications</a>
        
        <?php if ($worked_together && !$already_reported): ?>
            <button onclick="openReportModal(<?= $client_id ?>)" class="btn-report">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Report Issue
            </button>
        <?php endif; ?>
    </div>
    
    <div class="profile-card">
        <div class="profile-avatar-large">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        
        <h2><?= htmlspecialchars($client['full_name']) ?></h2>
        <p class="profile-email"><?= htmlspecialchars($client['email']) ?></p>
        
        <div class="profile-stats">
            <div class="stat-item">
                <span class="stat-value"><?= $client['total_jobs_posted'] ?></span>
                <span class="stat-label">Jobs Posted</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= $client['total_reports'] ?></span>
                <span class="stat-label">Reports Received</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?= date('Y', strtotime($client['created_at'])) ?></span>
                <span class="stat-label">Member Since</span>
            </div>
        </div>
        
        <?php if ($worked_jobs->num_rows > 0): ?>
            <?php $worked_jobs->data_seek(0); ?>
            <div class="worked-jobs-section">
                <h3>Jobs You Worked On</h3>
                <div class="jobs-list">
                    <?php while ($job = $worked_jobs->fetch_assoc()): ?>
                        <div class="job-item">
                            <h4><?= htmlspecialchars($job['job_title']) ?></h4>
                            <p><?= htmlspecialchars(substr($job['job_description'], 0, 150)) ?>...</p>
                            <div class="job-meta">
                                <span class="job-status <?= $job['status'] ?>"><?= ucfirst($job['status']) ?></span>
                                <span class="job-budget">$<?= number_format($job['budget'], 2) ?></span>
                                <span class="job-date">Hired: <?= date('M d, Y', strtotime($job['hired_at'])) ?></span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="no-jobs">You haven't worked on any jobs for this client yet.</p>
        <?php endif; ?>
    </div>
</div>

<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReportModal()">&times;</span>
        <h2>Report Fraudulent Activity</h2>
        <form action="actions/report_client.php" method="POST">
            <input type="hidden" name="client_id" id="report_client_id">
            
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
function openReportModal(clientId) {
    document.getElementById('report_client_id').value = clientId;
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