<?php
require '../../db_config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id) {
    $stmt = $conn->prepare(
        "DELETE FROM skill_categories WHERE skill_id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: ../dashboard.php?page=quiz_bank");
