<?php
// FreelanceHub/freelancer/freelancerTestrules.php
session_start();

// Check if signup data exists
if (!isset($_SESSION['signup_data']) || !isset($_SESSION['skill_category'])) {
    header('Location: freelancerSignup.php');
    exit();
}

// Clear any previous test session
unset($_SESSION['test_started']);
unset($_SESSION['test_questions']);
unset($_SESSION['answers']);
unset($_SESSION['current_question']);
unset($_SESSION['test_start_time']);

$pageCSS = 'freelancer/freelancerTestrules.css';
include '../header.php';
?>

<div class="test-rules-container">
    <div class="test-rules-content">
        <h1>Before You Start Your Skill Test</h1>
        <p class="intro-text">This short assessment helps us verify your skill level so we can match you with the right clients and job opportunities.</p>
        
        <div class="rules-list">
            <ul>
                <li>You will answer 10 multiple-choice questions based on <?= htmlspecialchars($_SESSION['skill_category']) ?>.</li>
                <li>You will have 2 minutes to complete the test. Once the timer starts, it cannot be paused.</li>
                <li>Do not use any external tools, apps, or AI assistance. Your answers should reflect your own skill level.</li>
                <li>You can take this test once every 24 hours if you fail. Make sure you are prepared before starting.</li>
                <li>You must score 70% or higher to pass. Failing will redirect you to recommended learning courses.</li>
                <li>If your browser closes or internet disconnects, the test will be automatically submitted.</li>
            </ul>
        </div>
        
        <form action="skilltest.php" method="GET">
            <button type="submit" class="btn-start">Start</button>
        </form>
        
        <p class="ready-text">Make sure you are ready before starting!</p>
    </div>
</div>

<?php include '../footer.php'; ?>