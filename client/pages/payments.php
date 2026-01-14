<h1>Payments</h1>

<?php
$payments = $conn->query("
    SELECT amount, created_at 
    FROM payments 
    WHERE client_id=$clientId
");
?>

<?php while($p = $payments->fetch_assoc()): ?>
    <div class="card">
        Amount: $<?= $p['amount'] ?><br>
        Date: <?= $p['created_at'] ?>
    </div>
<?php endwhile; ?>
