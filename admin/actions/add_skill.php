<?php
// FreelanceHub/admin/actions/add_skill.php
session_name("ADMIN_SESSION");
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skill_name = trim($_POST['skill_name']);
    
    if (empty($skill_name)) {
        header('Location: ../dashboard.php?page=skills&error=Skill name is required');
        exit();
    }
    
    $check_stmt = $conn->prepare("SELECT skill_id FROM skill_categories WHERE skill_name = ?");
    $check_stmt->bind_param("s", $skill_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        header('Location: ../dashboard.php?page=skills&error=Skill already exists');
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO skill_categories (skill_name) VALUES (?)");
    $stmt->bind_param("s", $skill_name);
    
    if ($stmt->execute()) {
        header('Location: ../dashboard.php?page=skills&msg=added');
    } else {
        header('Location: ../dashboard.php?page=skills&error=Failed to add skill');
    }
} else {
    header('Location: ../dashboard.php?page=skills');
}
exit();
?>