<?php
// FreelanceHub/admin/actions/add_course.php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../adminlogin.php');
    exit();
}

require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_title = trim($_POST['course_title']);
    $course_url = trim($_POST['course_url']);
    $skills = $_POST['skills'] ?? [];
    
    // Validate inputs
    if (empty($course_title) || empty($course_url)) {
        header('Location: ../dashboard.php?page=courses&error=Course title and URL are required');
        exit();
    }
    
    if (empty($skills)) {
        header('Location: ../dashboard.php?page=courses&error=Please select at least one skill');
        exit();
    }
    
    // Validate URL
    if (!filter_var($course_url, FILTER_VALIDATE_URL)) {
        header('Location: ../dashboard.php?page=courses&error=Invalid URL format');
        exit();
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert course
        $stmt = $conn->prepare("INSERT INTO courses (course_title, course_url) VALUES (?, ?)");
        $stmt->bind_param("ss", $_POST['course_title'], $_POST['course_url']);
        $stmt->execute();
        $course_id = $conn->insert_id;
        
        // Insert skill relationships
        $stmt = $conn->prepare("INSERT INTO skill_courses (skill_id, course_id) VALUES (?, ?)");
        foreach ($_POST['skills'] as $skill_id) {
            $skill_id = intval($skill_id);
            $stmt->bind_param("ii", $skill_id, $course_id);
            $stmt->execute();
        }
        
        $conn->commit();
        header('Location: ../dashboard.php?page=courses&msg=added');
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        header('Location: ../dashboard.php?page=courses&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: ../dashboard.php?page=courses');
    exit();
}
?>