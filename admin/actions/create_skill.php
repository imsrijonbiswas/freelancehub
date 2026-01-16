<?php
// FreelanceHub/admin/actions/create_skill.php
require '../../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['skill_name']);

    if ($name) {
        $stmt = $conn->prepare(
            "INSERT INTO skill_categories (skill_name) VALUES (?)"
        );
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }

    header("Location: ../dashboard.php?page=quiz_bank");
}
?>

<form method="post" class="form-box">
    <h2>Create Quiz Bank</h2>
    <input type="text" name="skill_name" required placeholder="Skill Name">
    <button>Create</button>
</form>
