<?php
// FreelanceHub/freelancer/test_result.php
session_name("FREELANCER_SESSION");
session_start();
require '../db_config.php';

if (!isset($_SESSION['test_questions']) || !isset($_SESSION['answers'])) {
    header('Location: freelancerSignup.php');
    exit();
}

$total_questions = count($_SESSION['test_questions']);
$correct_answers = 0;

foreach ($_SESSION['test_questions'] as $index => $question) {
    $question_num = $index + 1;
    $user_answer = $_SESSION['answers'][$question_num] ?? '';
    
    if ($user_answer == $question['correct_answer']) {
        $correct_answers++;
    }
}

$score_percentage = ($correct_answers / $total_questions) * 100;
$passed = $score_percentage >= 70;

$email = $_SESSION['signup_data']['email'];
$skill_id = $_SESSION['signup_data']['skill_id'];

$stmt = $conn->prepare("
    INSERT INTO freelancer_test_attempts (freelancer_email, skill_id, score, passed) 
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("sidi", $email, $skill_id, $score_percentage, $passed);
$stmt->execute();

if ($passed) {
    $fullname = $_SESSION['signup_data']['fullname'];
    $hashed_password = $_SESSION['signup_data']['password'];
    
    $stmt = $conn->prepare("
        INSERT INTO freelancers (full_name, email, password, skill_id, is_verified) 
        VALUES (?, ?, ?, ?, TRUE)
    ");
    $stmt->bind_param("sssi", $fullname, $email, $hashed_password, $skill_id);
    $stmt->execute();
}

$test_data = [
    'score' => $score_percentage,
    'passed' => $passed,
    'skill_category' => $_SESSION['skill_category']
];

session_unset();

$_SESSION['test_result'] = $test_data;

$pageCSS = 'freelancer/test_result.css';
include '../header.php';
?>

<div class="result-container">
    <div class="result-box">
        <?php if ($test_data['passed']): ?>
            <!-- PASSED -->
            <div class="result-content success">
                <h1>Congratulations! You passed the skill verification test 🎉</h1>
                
                <div class="score-display">
                    <p class="score-label">Your Score:</p>
                    <p class="score-value"><?= number_format($test_data['score'], 0) ?>%</p>
                    <p class="score-info">
                        You are now eligible to apply for jobs in<br>
                        <strong><?= htmlspecialchars($test_data['skill_category']) ?></strong>
                    </p>
                </div>
                
                <a href="/FreelanceHub/login.php" class="btn-finalize">Finalize Your Profile</a>
            </div>
        <?php else: ?>
            <!-- FAILED -->
            <div class="result-content failed">
                <h1>Failed!</h1>
                
                <div class="fail-icon">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                        <circle cx="40" cy="40" r="38" stroke="#ff3b3b" stroke-width="4"/>
                        <path d="M25 25L55 55M55 25L25 55" stroke="#ff3b3b" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>
                
                <div class="score-display">
                    <p class="score-label">Your Score:</p>
                    <p class="score-value failed-score"><?= number_format($test_data['score'], 0) ?>%</p>
                    <p class="score-info">
                        Don't worry — this is common for new freelancers.<br>
                        You can improve your skills and try again.
                    </p>
                </div>
                
                <p class="retry-info">
                    You need to complete the recommended course in order to 
                    <strong>Sign In</strong> and pass the <strong>Test with 70%</strong> 
                    to show recommended courses.
                </p>
                
                <a href="course_offerings.php?skill_id=<?= $skill_id ?>" class="btn-courses">View Recommended Courses</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; ?>