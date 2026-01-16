<?php
// FreelanceHub/freelancer/skilltest.php
session_name("FREELANCER_SESSION");
session_start();
require '../db_config.php';

// Check if signup data exists
if (!isset($_SESSION['signup_data']) || !isset($_SESSION['skill_category'])) {
    header('Location: freelancerSignup.php');
    exit();
}

$skill_id = $_SESSION['signup_data']['skill_id'];

// Initialize test session if not exists
if (!isset($_SESSION['test_started'])) {
    $_SESSION['test_started'] = true;
    $_SESSION['test_start_time'] = time();
    $_SESSION['current_question'] = 1;
    $_SESSION['answers'] = [];
    
    // Fetch 10 random questions for this skill category
    $stmt = $conn->prepare("
        SELECT q.question_id, q.question_text, q.skill_id
        FROM questions q
        WHERE q.skill_id = ?
        ORDER BY RAND()
        LIMIT 10
    ");
    $stmt->bind_param("i", $skill_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows < 10) {
        // Not enough questions
        header('Location: freelancerSignup.php?error=Not enough questions available for this skill. Please contact admin.');
        exit();
    }
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $question_id = $row['question_id'];
        
        // Get options for this question
        $options_stmt = $conn->prepare("
            SELECT option_label, option_text, is_correct 
            FROM question_options 
            WHERE question_id = ? 
            ORDER BY option_label
        ");
        $options_stmt->bind_param("i", $question_id);
        $options_stmt->execute();
        $options_result = $options_stmt->get_result();
        
        $options = [];
        $correct_answer = '';
        while ($option = $options_result->fetch_assoc()) {
            $options[$option['option_label']] = $option['option_text'];
            if ($option['is_correct']) {
                $correct_answer = $option['option_label'];
            }
        }
        
        $row['options'] = $options;
        $row['correct_answer'] = $correct_answer;
        $questions[] = $row;
    }
    
    $_SESSION['test_questions'] = $questions;
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $current_q = $_SESSION['current_question'];
    $_SESSION['answers'][$current_q] = $_POST['answer'];
    
    // Move to next question
    if ($current_q < 10) {
        $_SESSION['current_question']++;
    } else {
        // Test completed
        header('Location: test_result.php');
        exit();
    }
}

// Get current question
$current_question_num = $_SESSION['current_question'];
$current_question_data = $_SESSION['test_questions'][$current_question_num - 1];

// Calculate remaining time (2 minutes = 120 seconds)
$elapsed_time = time() - $_SESSION['test_start_time'];
$remaining_time = 120 - $elapsed_time;

// Auto-submit if time is up
if ($remaining_time <= 0) {
    header('Location: test_result.php');
    exit();
}

// Calculate progress percentage
$progress_percentage = ($current_question_num / 10) * 100;

$pageCSS = 'freelancer/skilltest.css';
include '../header.php';
?>

<div class="test-container">
    <div class="test-box">
        <div class="test-header">
            <div class="test-title">
                <h2>Skill Test: <?= htmlspecialchars($_SESSION['skill_category']) ?></h2>
            </div>
            <div class="timer-box">
                <span id="timer">Time Left: <span id="time-display">2:00</span></span>
            </div>
        </div>
        
        <div class="question-info">
            <span>Question <?= $current_question_num ?> of 10</span>
        </div>
        
        <form method="POST" id="question-form">
            <div class="question-section">
                <p class="question-text"><?= htmlspecialchars($current_question_data['question_text']) ?></p>
                
                <div class="options-list">
                    <?php foreach ($current_question_data['options'] as $label => $text): ?>
                        <label class="option-item">
                            <input type="radio" name="answer" value="<?= $label ?>" 
                                <?= (isset($_SESSION['answers'][$current_question_num]) && $_SESSION['answers'][$current_question_num] == $label) ? 'checked' : '' ?> required>
                            <span class="option-text"><?= htmlspecialchars($text) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn-next">Next Question</button>
            </div>
        </form>
        
        <div class="progress-section">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress_percentage ?>%"></div>
            </div>
            <p class="warning-text">You cannot go back to previous questions. Each question is timed.</p>
        </div>
    </div>
</div>

<script>
let remainingSeconds = <?= $remaining_time ?>;

function updateTimer() {
    if (remainingSeconds <= 0) {
        document.getElementById('question-form').submit();
        return;
    }
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    
    document.getElementById('time-display').textContent = 
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (remainingSeconds <= 30) {
        document.querySelector('.timer-box').style.backgroundColor = '#ff4444';
    }
    
    remainingSeconds--;
}

setInterval(updateTimer, 1000);
updateTimer();

window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<?php include '../footer.php'; ?>