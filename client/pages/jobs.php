<h1>Your Jobs</h1>

<?php
$result = $conn->query("SELECT title, status FROM jobs WHERE client_id=$clientId");
?>

<?php if ($result->num_rows > 0): ?>
    <?php while($job = $result->fetch_assoc()): ?>
        <div class="card">
            <strong><?= htmlspecialchars($job['title']) ?></strong><br>
            Status: <?= htmlspecialchars($job['status']) ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No jobs posted yet.</p>
<?php endif; ?>
