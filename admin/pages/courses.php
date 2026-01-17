<?php
// FreelanceHub/admin/pages/courses.php

$skills_query = $conn->query("SELECT skill_id, skill_name FROM skill_categories ORDER BY skill_name");

$courses_query = $conn->query("
    SELECT 
        c.course_id, 
        c.course_title, 
        c.course_url, 
        c.created_at,
        GROUP_CONCAT(s.skill_name SEPARATOR ', ') as skills
    FROM courses c
    LEFT JOIN skill_courses sc ON c.course_id = sc.course_id
    LEFT JOIN skill_categories s ON sc.skill_id = s.skill_id
    GROUP BY c.course_id
    ORDER BY c.created_at DESC
");

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!-- Messages -->
<?php if ($message == 'added'): ?>
    <div class="success-message">Course added successfully!</div>
<?php elseif ($message == 'deleted'): ?>
    <div class="success-message">Course deleted successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="course-section">
    <div class="section-header">
        <h2>Add New Course</h2>
    </div>
    
    <form action="actions/add_course.php" method="POST" class="course-form">
        <div class="form-group">
            <label for="course_title">Course Title *</label>
            <input type="text" name="course_title" id="course_title" required 
                placeholder="e.g., Complete Web Development Bootcamp">
        </div>
        
        <div class="form-group">
            <label for="course_url">Course URL *</label>
            <input type="url" name="course_url" id="course_url" required 
                placeholder="https://www.example.com/course">
        </div>
        
        <div class="form-group">
            <label>Related Skills *</label>
            <div class="skills-checkbox-grid">
                <?php 
                $skills_query->data_seek(0); // Reset pointer
                while ($skill = $skills_query->fetch_assoc()): 
                ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="skills[]" value="<?= $skill['skill_id'] ?>">
                        <span><?= htmlspecialchars($skill['skill_name']) ?></span>
                    </label>
                <?php endwhile; ?>
            </div>
            <small class="form-hint">Select at least one skill that this course relates to</small>
        </div>
        
        <button type="submit" class="btn-primary">Add Course</button>
    </form>
</div>

<div class="course-section">
    <div class="section-header">
        <h2>All Courses (<?= $courses_query->num_rows ?>)</h2>
    </div>
    
    <?php if ($courses_query && $courses_query->num_rows > 0): ?>
        <div class="courses-list">
            <?php while ($course = $courses_query->fetch_assoc()): ?>
                <div class="course-card">
                    <div class="course-header">
                        <h3 class="course-title"><?= htmlspecialchars($course['course_title']) ?></h3>
                        <span class="course-date"><?= date('M d, Y', strtotime($course['created_at'])) ?></span>
                    </div>
                    
                    <div class="course-content">
                        <div class="course-url">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                                <path d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                            </svg>
                            <a href="<?= htmlspecialchars($course['course_url']) ?>" target="_blank">
                                <?= htmlspecialchars($course['course_url']) ?>
                            </a>
                        </div>
                        
                        <?php if ($course['skills']): ?>
                            <div class="course-skills">
                                <?php 
                                $skills = explode(', ', $course['skills']);
                                foreach ($skills as $skill): 
                                ?>
                                    <span class="skill-tag"><?= htmlspecialchars($skill) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-actions">
                        <a href="actions/delete_course.php?id=<?= $course['course_id'] ?>" 
                           class="btn-danger btn-small"
                           onclick="return confirm('Are you sure you want to delete this course?')">
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
            <p>No courses found. Add your first course above!</p>
        </div>
    <?php endif; ?>
</div>