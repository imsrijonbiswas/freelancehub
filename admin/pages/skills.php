<?php
// FreelanceHub/admin/pages/skills.php

// Get all skills with question count and course count
$skills_query = $conn->query("
    SELECT 
        s.skill_id,
        s.skill_name,
        s.created_at,
        COUNT(DISTINCT q.question_id) as question_count,
        COUNT(DISTINCT sc.course_id) as course_count
    FROM skill_categories s
    LEFT JOIN questions q ON s.skill_id = q.skill_id
    LEFT JOIN skill_courses sc ON s.skill_id = sc.skill_id
    GROUP BY s.skill_id
    ORDER BY s.skill_name ASC
");

// Get total count
$total_skills = $skills_query->num_rows;

// Get messages
$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!-- Messages -->
<?php if ($message == 'added'): ?>
    <div class="success-message">Skill added successfully!</div>
<?php elseif ($message == 'deleted'): ?>
    <div class="success-message">Skill deleted successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Add New Skill Section -->
<div class="skills-section">
    <div class="section-header">
        <h2>Add New Skill Category</h2>
    </div>
    
    <form action="actions/add_skill.php" method="POST" class="skill-form">
        <div class="form-row-inline">
            <div class="form-group" style="flex: 1;">
                <label for="skill_name">Skill Name *</label>
                <input type="text" name="skill_name" id="skill_name" required 
                    placeholder="e.g., Web Development, Graphic Design, Content Writing">
            </div>
            <button type="submit" class="btn-primary" style="align-self: flex-end;">Add Skill</button>
        </div>
    </form>
</div>

<!-- Skills List Section -->
<div class="skills-section">
    <div class="section-header">
        <h2>All Skill Categories (<?= $total_skills ?>)</h2>
    </div>
    
    <?php if ($skills_query && $skills_query->num_rows > 0): ?>
        <div class="skills-grid">
            <?php 
            $skills_query->data_seek(0); // Reset pointer
            while ($skill = $skills_query->fetch_assoc()): 
            ?>
                <div class="skill-card">
                    <div class="skill-header">
                        <h3 class="skill-name"><?= htmlspecialchars($skill['skill_name']) ?></h3>
                    </div>
                    
                    <div class="skill-stats">
                        <div class="skill-stat-item">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                            </svg>
                            <span><?= $skill['question_count'] ?> Questions</span>
                        </div>
                        <div class="skill-stat-item">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                            </svg>
                            <span><?= $skill['course_count'] ?> Courses</span>
                        </div>
                    </div>
                    
                    <div class="skill-footer">
                        <span class="skill-date">Added: <?= date('M d, Y', strtotime($skill['created_at'])) ?></span>
                        <a href="actions/delete_skill.php?id=<?= $skill['skill_id'] ?>" 
                           class="btn-danger btn-small"
                           onclick="return confirm('Are you sure you want to delete this skill? This will also delete all related questions and course associations.')">
                            Delete
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
            <p>No skills found. Add your first skill above!</p>
        </div>
    <?php endif; ?>
</div>