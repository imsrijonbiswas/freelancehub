<h1>Messages</h1>

<?php
$msgs = $conn->query("
    SELECT sender, message, created_at 
    FROM messages 
    WHERE client_id=$clientId 
    ORDER BY created_at DESC
");
?>

<?php while($m = $msgs->fetch_assoc()): ?>
    <div class="card">
        <strong><?= htmlspecialchars($m['sender']) ?></strong><br>
        <?= htmlspecialchars($m['message']) ?><br>
        <small><?= $m['created_at'] ?></small>
    </div>
<?php endwhile; ?>
