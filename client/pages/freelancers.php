<?php
// FreelanceHub/client/pages/freelancers.php

$client_id = $_SESSION['client_id'];

// Get filter
$filter_skill = isset($_GET['skill']) ? intval($_GET['skill']) : 0;

// Get all skills for filter
$skills_query = $conn->query("SELECT skill_id, skill_name FROM skill_categories ORDER BY skill_name ASC");

// Get freelancers with test scores
if ($filter_skill > 0) {
    $freelancers_query = $conn->query("
        SELECT 
            f.freelancer_id,
            f.full_name,
            f.email,
            f.bio,
            s.skill_name,
            fta.score as test_score,
            (SELECT COUNT(*) FROM hired_freelancers WHERE freelancer_id = f.freelancer_id) as completed_jobs,
            (SELECT AVG(rating) FROM ratings r 
             JOIN hired_freelancers hf ON r.hire_id = hf.hire_id 
             WHERE hf.freelancer_id = f.freelancer_id) as avg_rating
        FROM freelancers f
        LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
        LEFT JOIN freelancer_test_attempts fta ON f.email = fta.freelancer_email AND fta.passed = TRUE
        WHERE f.is_verified = TRUE AND f.skill_id = $filter_skill
        GROUP BY f.freelancer_id
        ORDER BY fta.score DESC, f.created_at DESC
    ");
} else {
    $freelancers_query = $conn->query("
        SELECT 
            f.freelancer_id,
            f.full_name,
            f.email,
            f.bio,
            s.skill_name,
            fta.score as test_score,
            (SELECT COUNT(*) FROM hired_freelancers WHERE freelancer_id = f.freelancer_id) as completed_jobs,
            (SELECT AVG(rating) FROM ratings r 
             JOIN hired_freelancers hf ON r.hire_id = hf.hire_id 
             WHERE hf.freelancer_id = f.freelancer_id) as avg_rating
        FROM freelancers f
        LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
        LEFT JOIN freelancer_test_attempts fta ON f.email = fta.freelancer_email AND fta.passed = TRUE
        WHERE f.is_verified = TRUE
        GROUP BY f.freelancer_id
        ORDER BY fta.score DESC, f.created_at DESC
    ");
}
?>

<link rel="stylesheet" href="css/freelancers.css">

<div class="freelancers-container">
    <div class="page-header">
        <div>
            <h1>Find Freelancers</h1>
            <p class="subtitle">Browse verified freelancers and view their test scores</p>
        </div>
        <form method="GET" action="dashboard.php" class="filter-form">
            <input type="hidden" name="page" value="freelancers">
            <select name="skill" onchange="this.form.submit()">
                <option value="0">All Skills</option>
                <?php 
                $skills_query->data_seek(0);
                while ($skill = $skills_query->fetch_assoc()): 
                ?>
                    <option value="<?= $skill['skill_id'] ?>" <?= $filter_skill == $skill['skill_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($skill['skill_name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <?php if ($freelancers_query && $freelancers_query->num_rows > 0): ?>
        <div class="freelancers-grid">
            <?php while ($freelancer = $freelancers_query->fetch_assoc()): ?>
                <div class="freelancer-card">
                    <div class="freelancer-avatar-large">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    
                    <h3><?= htmlspecialchars($freelancer['full_name']) ?></h3>
                    <span class="freelancer-skill"><?= htmlspecialchars($freelancer['skill_name']) ?></span>
                    
                    <?php if ($freelancer['bio']): ?>
                        <p class="freelancer-bio"><?= htmlspecialchars(substr($freelancer['bio'], 0, 100)) ?>...</p>
                    <?php endif; ?>
                    
                    <div class="freelancer-stats">
                        <div class="stat-item">
                            <span class="stat-label">Test Score</span>
                            <span class="stat-value score"><?= number_format($freelancer['test_score'] ?? 0, 0) ?>%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Jobs Done</span>
                            <span class="stat-value"><?= $freelancer['completed_jobs'] ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Rating</span>
                            <span class="stat-value rating">
                                <?php if ($freelancer['avg_rating']): ?>
                                    ⭐ <?= number_format($freelancer['avg_rating'], 1) ?>
                                <?php else: ?>
                                    No ratings yet
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="freelancer-actions">
                        <a href="mailto:<?= htmlspecialchars($freelancer['email']) ?>" class="btn-contact">
                            Contact
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
            <p>No freelancers found for this skill category</p>
        </div>
    <?php endif; ?>
</div>