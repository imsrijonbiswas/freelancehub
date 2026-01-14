<h1>Saved Freelancers</h1>

<?php
$freelancers = $conn->query("SELECT name, skill, rating FROM freelancers LIMIT 10");
?>

<?php while($f = $freelancers->fetch_assoc()): ?>
    <div class="card">
        <strong><?= htmlspecialchars($f['name']) ?></strong><br>
        <?= htmlspecialchars($f['skill']) ?><br>
        ⭐ <?= $f['rating'] ?>
    </div>
<?php endwhile; ?>
