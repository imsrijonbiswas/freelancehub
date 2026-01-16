<?php
// FreelanceHub/admin/actions/add_question.php
session_name("ADMIN_SESSION");
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skill_id = intval($_POST['skill_id']);
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer'];
    
    // Validate inputs
    if (empty($skill_id) || empty($question_text) || empty($option_a) || 
        empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_answer)) {
        header('Location: ../dashboard.php?page=quizzes&error=All fields are required');
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert question
        $stmt = $conn->prepare("INSERT INTO questions (skill_id, question_text) VALUES (?, ?)");
        $stmt->bind_param("is", $skill_id, $question_text);
        $stmt->execute();
        $question_id = $conn->insert_id;
        
        // Insert options
        $options = [
            'A' => $option_a,
            'B' => $option_b,
            'C' => $option_c,
            'D' => $option_d
        ];
        
        $stmt = $conn->prepare("INSERT INTO question_options (question_id, option_label, option_text, is_correct) VALUES (?, ?, ?, ?)");
        
        foreach ($options as $label => $text) {
            $is_correct = ($label === $correct_answer) ? 1 : 0;
            $stmt->bind_param("issi", $question_id, $label, $text, $is_correct);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        header('Location: ../dashboard.php?page=quizzes&msg=added');
        exit();
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        header('Location: ../dashboard.php?page=quizzes&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: ../dashboard.php?page=quizzes');
    exit();
}
?>