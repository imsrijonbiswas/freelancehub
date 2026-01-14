<?php
session_start();

// TEMPORARY (remove later when login system is ready)
// $_SESSION['user_id'] = 1;
// $_SESSION['skill_category'] = 'Web Development';
// ?>


<?php
$pageCSS = 'freelancer/freelancerTestrules.css';
include '../header.php';
?>

unset($_SESSION['test_started']);
unset($_SESSION['test_questions']);
unset($_SESSION['answers']);
unset($_SESSION['current_question']);

<div class="test-rules-container">
    <div class="test-rules-content">
        <h1>Before You Start Your Skill Test</h1>
        <p class="intro-text">This short assessment helps us verify your skill level so we can match you with the right clients and job opportunities.</p>
        
        <div class="rules-list">
            <ul>
                <li>You will answer 15 multiple-choice questions based on your selected skill category.</li>
                <li>You will have 10 minutes to complete the test. Once the timer starts, it cannot be paused.</li>
                <li>Do not use any external tools, apps, or AI assistance. Your answers should reflect your own skill level.</li>
                <li>You can take this test once every 30 days. Make sure you are prepared before starting.</li>
                <li>You must score 70% or higher to pass. Failing will redirect you to recommended learning courses.</li>
                <li>If your browser closes or internet disconnects, the test will be automatically submitted.</li>
            </ul>
        </div>
        
        <form action="skilltest.php" method="get">
            <button type="submit" class="btn-start">Start</button>
        </form>
        
        <p class="ready-text">Make sure you are ready before starting !</p>
    </div>
</div>

<?php 
include '../footer.php'; 
?>