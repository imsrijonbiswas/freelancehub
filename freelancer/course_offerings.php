<?php
// FreelanceHub/freelancer/course_offerings.php
require '../db_config.php';

$skill_id = isset($_GET['skill_id']) ? intval($_GET['skill_id']) : 0;

if ($skill_id == 0) {
    header('Location: freelancerSignup.php');
    exit();
}

// Get skill name
$skill_stmt = $conn->prepare("SELECT skill_name FROM skill_categories WHERE skill_id = ?");
$skill_stmt->bind_param("i", $skill_id);
$skill_stmt->execute();
$skill_result = $skill_stmt->get_result();
$skill = $skill_result->fetch_assoc();

if (!$skill) {
    header('Location: freelancerSignup.php');
    exit();
}

// Get courses for this skill
$courses_query = $conn->prepare("
    SELECT c.course_id, c.course_title, c.course_url
    FROM courses c
    JOIN skill_courses sc ON c.course_id = sc.course_id
    WHERE sc.skill_id = ?
    ORDER BY c.created_at DESC
");
$courses_query->bind_param("i", $skill_id);
$courses_query->execute();
$courses_result = $courses_query->get_result();

$pageCSS = 'freelancer/course_offerings.css';
include '../header.php';
?>

<div class="courses-container">
    <div class="courses-content">
        <h1>Recommended Learning Courses</h1>
        <p class="subtitle">
            Enhance your skills in <strong><?= htmlspecialchars($skill['skill_name']) ?></strong> 
            with these recommended courses
        </p>
        
        <?php if ($courses_result->num_rows > 0): ?>
            <div class="courses-list">
                <?php while ($course = $courses_result->fetch_assoc()): ?>
                    <div class="course-item">
                        <div class="course-info">
                            <h3><?= htmlspecialchars($course['course_title']) ?></h3>
                        </div>
                        <a href="<?= htmlspecialchars($course['course_url']) ?>" 
                           target="_blank" 
                           class="btn-view-course">
                            View Course
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                                <path d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                            </svg>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No courses available for this skill yet. Please check back later!</p>
            </div>
        <?php endif; ?>
        
        <div class="retry-section">
            <p>After completing a course, you can retry the test in 24 hours.</p>
            <a href="freelancerSignup.php" class="btn-back">Back to Sign Up</a>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>