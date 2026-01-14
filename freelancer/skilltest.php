<?php
// skilltest.php
session_start();

// Checking if user is logged in and has selected a skill category
if (!isset($_SESSION['user_id']) || !isset($_SESSION['skill_category'])) {
    header('Location: freelancerSignup.php');
    exit();
}

// Database connection
include '../db_config.php';

// Get skill category from session
$skill_category = $_SESSION['skill_category'];
$user_id = $_SESSION['user_id'];

// Initialize test session if not exists
if (!isset($_SESSION['test_started'])) {
    $_SESSION['test_started'] = true;
    $_SESSION['test_start_time'] = time();
    $_SESSION['current_question'] = 1;
    $_SESSION['answers'] = [];
    
    // Fetch 15 random questions for this skill category
    $stmt = $conn->prepare("SELECT * FROM questions WHERE skill_category = ? ORDER BY RAND() LIMIT 15");
    $stmt->bind_param("s", $skill_category);
    $stmt->execute();
    $result = $stmt->get_result();

//     if (!isset($_SESSION['test_started'])) {
//     $_SESSION['test_started'] = true;
//     $_SESSION['test_start_time'] = time();
//     $_SESSION['current_question'] = 1;
//     $_SESSION['answers'] = [];

//     // TEMP QUESTIONS (remove later)
//     $_SESSION['test_questions'] = [
//         [
//             'question_text' => 'What does HTML stand for?',
//             'option_a' => 'Hyper Text Markup Language',
//             'option_b' => 'High Text Machine Language',
//             'option_c' => 'Hyperlink Text Markup Language',
//             'option_d' => 'Home Tool Markup Language',
//             'correct_answer' => 'A'
//         ],
//         [
//             'question_text' => 'Which language runs in the browser?',
//             'option_a' => 'PHP',
//             'option_b' => 'Java',
//             'option_c' => 'JavaScript',
//             'option_d' => 'C++',
//             'correct_answer' => 'C'
//         ]
//         // add more later (15 total)
//     ];
// }

    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
    $_SESSION['test_questions'] = $questions;
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $current_q = $_SESSION['current_question'];
    $_SESSION['answers'][$current_q] = $_POST['answer'];
    
    // Move to next question
    if ($current_q < 15) {
        $_SESSION['current_question']++;
    } else {
        // Test completed, calculate score
        header('Location: test_result.php');
        exit();
    }
}

// Get current question
$current_question_num = $_SESSION['current_question'];
$current_question_data = $_SESSION['test_questions'][$current_question_num - 1];

// Calculate remaining time (10 minutes = 600 seconds)
$elapsed_time = time() - $_SESSION['test_start_time'];
$remaining_time = 600 - $elapsed_time;

// Auto-submit if time is up
if ($remaining_time <= 0) {
    header('Location: test_result.php');
    exit();
}

// Calculate progress percentage
$progress_percentage = ($current_question_num / 15) * 100;

$pageCSS = 'freelancer/skilltest.css';
include '../header.php';
?>

<div class="test-container">
    <div class="test-box">
        <div class="test-header">
            <div class="test-title">
                <h2>Skill Test : <?php echo htmlspecialchars($skill_category); ?></h2>
            </div>
            <div class="timer-box">
                <span id="timer">Time Left: <span id="time-display">10:00</span></span>
            </div>
        </div>
        
        <div class="question-info">
            <span>Question <?php echo $current_question_num; ?> of 15</span>
        </div>
        
        <form method="POST" id="question-form">
            <div class="question-section">
                <p class="question-text"><?php echo htmlspecialchars($current_question_data['question_text']); ?></p>
                
                <div class="options-list">
                    <label class="option-item">
                        <input type="radio" name="answer" value="A" 
                            <?php echo (isset($_SESSION['answers'][$current_question_num]) && $_SESSION['answers'][$current_question_num] == 'A') ? 'checked' : ''; ?> required>
                        <span class="option-text"><?php echo htmlspecialchars($current_question_data['option_a']); ?></span>
                    </label>
                    
                    <label class="option-item">
                        <input type="radio" name="answer" value="B"
                            <?php echo (isset($_SESSION['answers'][$current_question_num]) && $_SESSION['answers'][$current_question_num] == 'B') ? 'checked' : ''; ?>>
                        <span class="option-text"><?php echo htmlspecialchars($current_question_data['option_b']); ?></span>
                    </label>
                    
                    <label class="option-item">
                        <input type="radio" name="answer" value="C"
                            <?php echo (isset($_SESSION['answers'][$current_question_num]) && $_SESSION['answers'][$current_question_num] == 'C') ? 'checked' : ''; ?>>
                        <span class="option-text"><?php echo htmlspecialchars($current_question_data['option_c']); ?></span>
                    </label>
                    
                    <label class="option-item">
                        <input type="radio" name="answer" value="D"
                            <?php echo (isset($_SESSION['answers'][$current_question_num]) && $_SESSION['answers'][$current_question_num] == 'D') ? 'checked' : ''; ?>>
                        <span class="option-text"><?php echo htmlspecialchars($current_question_data['option_d']); ?></span>
                    </label>
                </div>
                
                <button type="submit" class="btn-next">Next Question</button>
            </div>
        </form>
        
        <div class="progress-section">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progress_percentage; ?>%"></div>
            </div>
            <p class="warning-text">You cannot go back to previous questions. Each question is timed.</p>
        </div>
    </div>
</div>

<script>
// Timer countdown
let remainingSeconds = <?php echo $remaining_time; ?>;

function updateTimer() {
    if (remainingSeconds <= 0) {
        // Auto-submit form when time is up
        document.getElementById('question-form').submit();
        return;
    }
    
    const minutes = Math.floor(remainingSeconds / 60);
    const seconds = remainingSeconds % 60;
    
    document.getElementById('time-display').textContent = 
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    // Change timer color when less than 1 minute
    if (remainingSeconds <= 60) {
        document.getElementById('timer').style.backgroundColor = '#ff4444';
    }
    
    remainingSeconds--;
}

// Update timer every second
setInterval(updateTimer, 1000);
updateTimer(); // Initial call

// Prevent page refresh/close
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});

// Auto-submit on visibility change (browser minimize, tab switch)
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Page hidden - test continues');
    }
});
</script>

<?php
include '../footer.php';
?>