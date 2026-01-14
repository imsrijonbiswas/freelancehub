<?php
$result = $conn->query("
    SELECT sc.skill_id, sc.skill_name, COUNT(q.question_id) total
    FROM skill_categories sc
    LEFT JOIN questions q ON sc.skill_id = q.skill_id
    GROUP BY sc.skill_id
");
?>

<div class="header-row">
    <h1>Quiz Banks</h1>
    <a href="actions/create_skill.php" class="btn-primary">Create New</a>
</div>

<?php while ($row = $result->fetch_assoc()): ?>
<div class="quiz-card">
    <div>
        <strong><?= htmlspecialchars($row['skill_name']) ?></strong>
        <small><?= $row['total'] ?> Quizzes</small>
    </div>

    <div>
        <a href="#" class="btn-light">Edit</a>
        <a href="actions/delete_skill.php?id=<?= $row['skill_id'] ?>"
           class="btn-danger"
           onclick="return confirm('Delete this quiz bank?')">
           Delete
        </a>
    </div>
</div>
<?php endwhile; ?>
