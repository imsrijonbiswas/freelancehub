<?php
// FreelanceHub/admin/actions/delete_question.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if (isset($_GET['id'])) {
    $question_id = intval($_GET['id']);
    
    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=quizzes&msg=deleted');
    } else {
        header('Location: ../dashboard.php?page=quizzes&error=Failed to delete question');
    }
} else {
    header('Location: ../dashboard.php?page=quizzes');
}
exit();
?>