<?php
// FreelanceHub/client/pages/post-job.php

$skills_query = $conn->query("SELECT skill_id, skill_name FROM skill_categories ORDER BY skill_name ASC");

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<link rel="stylesheet" href="/FreelanceHub/client/css/post_job.css">

<?php if ($message == 'posted'): ?>
    <div class="success-message">Job posted successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="post-job-container">
    <div class="page-header">
        <h1>Post a New Job</h1>
        <p class="subtitle">Fill in the details below to post your job and start receiving applications</p>
    </div>

    <form class="post-job-form" method="POST" action="actions/create_job.php">
        <div class="form-section">
            <h3>Job Information</h3>
            
            <div class="form-group">
                <label for="job_title">Job Title *</label>
                <input type="text" id="job_title" name="job_title" required 
                       placeholder="e.g., Build a responsive website">
            </div>
            
            <div class="form-group">
                <label for="skill_id">Required Skill *</label>
                <select id="skill_id" name="skill_id" required>
                    <option value="">Select a skill category</option>
                    <?php while ($skill = $skills_query->fetch_assoc()): ?>
                        <option value="<?= $skill['skill_id'] ?>">
                            <?= htmlspecialchars($skill['skill_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="job_description">Job Description *</label>
                <textarea id="job_description" name="job_description" rows="6" required
                          placeholder="Describe what you need done, requirements, deliverables, etc."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="budget">Budget ($) *</label>
                    <input type="number" id="budget" name="budget" step="0.01" min="1" required
                           placeholder="0.00">
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="dashboard.php?page=home" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Post Job</button>
        </div>
    </form>
</div>