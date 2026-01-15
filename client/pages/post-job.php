<?php
// FreelanceHub/client/pages/post-job.php

// Fetch categories
$categoriesQuery = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<link rel="stylesheet" href="css/post_job.css">

<div class="post-job-container">
    <div class="post-job-header">
        <div>
            <h1>Post a Job</h1>
            <p class="subtitle">Create a new job listing and find the right freelancer for your project</p>
        </div>
    </div>

    <div class="post-job-card">
        <form action="process_post_job.php" method="POST" class="job-form">
            
            <!-- Job Title -->
            <div class="form-section">
                <label class="form-label">Job Title</label>
                <input type="text" name="title" class="form-input" placeholder="Write an eye-catching title" required>
            </div>

            <!-- Description -->
            <div class="form-section">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="6" placeholder="Write a detailed description of the job and expectations" required></textarea>
            </div>

            <!-- Category -->
            <div class="form-section">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select a category</option>
                    <?php while ($cat = $categoriesQuery->fetch_assoc()): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Skills Required -->
            <div class="form-section">
                <label class="form-label">Skills Required</label>
                <div class="skills-input-container">
                    <input type="text" id="skill-input" class="form-input" placeholder="Add Skills Here">
                    <button type="button" class="btn-add-skill" onclick="addSkill()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                </div>
                <div id="skills-container" class="skills-container"></div>
                <input type="hidden" name="required_skills" id="required_skills">
            </div>

            <!-- Price Range -->
            <div class="form-section">
                <label class="form-label">Price Range ($)</label>
                <div class="price-range-container">
                    <input type="number" name="min_price" class="form-input" placeholder="Minimum Price" min="0" step="0.01" required>
                    <span class="price-separator">to</span>
                    <input type="number" name="max_price" class="form-input" placeholder="Maximum Price" min="0" step="0.01" required>
                </div>
            </div>

            <!-- Deadline -->
            <div class="form-section">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-input" min="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="window.location.href='?page=dashboard'">Cancel</button>
                <button type="submit" class="btn-post">Post Job</button>
            </div>
        </form>
    </div>
</div>

<script>
let skills = [];

function addSkill() {
    const input = document.getElementById('skill-input');
    const skill = input.value.trim();
    
    if (skill && !skills.includes(skill)) {
        skills.push(skill);
        updateSkillsDisplay();
        input.value = '';
    }
}

function removeSkill(skill) {
    skills = skills.filter(s => s !== skill);
    updateSkillsDisplay();
}

function updateSkillsDisplay() {
    const container = document.getElementById('skills-container');
    const hiddenInput = document.getElementById('required_skills');
    
    container.innerHTML = skills.map(skill => `
        <div class="skill-tag">
            ${skill}
            <button type="button" class="remove-skill" onclick="removeSkill('${skill}')">×</button>
        </div>
    `).join('');
    
    hiddenInput.value = skills.join(',');
}

// Allow Enter key to add skills
document.getElementById('skill-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill();
    }
});
</script>