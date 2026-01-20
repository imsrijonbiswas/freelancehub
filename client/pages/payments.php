<?php
// FreelanceHub/client/pages/payments.php

$client_id = $_SESSION['client_id'];

$payments_query = $conn->query("
    SELECT 
        p.*,
        hf.job_id,
        j.job_title,
        f.full_name as freelancer_name
    FROM payments p
    JOIN hired_freelancers hf ON p.hire_id = hf.hire_id
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN freelancers f ON hf.freelancer_id = f.freelancer_id
    WHERE hf.client_id = $client_id
    ORDER BY p.paid_at DESC
");

$total_query = $conn->query("
    SELECT COALESCE(SUM(p.amount), 0) as total
    FROM payments p
    JOIN hired_freelancers hf ON p.hire_id = hf.hire_id
    WHERE hf.client_id = $client_id AND p.payment_status = 'completed'
");
$total_spent = $total_query->fetch_assoc()['total'];

$pending_query = $conn->query("
    SELECT 
        hf.hire_id,
        hf.job_id,
        j.job_title,
        j.budget,
        f.full_name as freelancer_name,
        hf.hired_at
    FROM hired_freelancers hf
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN freelancers f ON hf.freelancer_id = f.freelancer_id
    LEFT JOIN payments p ON hf.hire_id = p.hire_id
    WHERE hf.client_id = $client_id 
    AND hf.status = 'active'
    AND p.payment_id IS NULL
    ORDER BY hf.hired_at DESC
");

$message = $_GET['msg'] ?? '';
?>

<link rel="stylesheet" href="css/payments.css">

<?php if ($message == 'paid'): ?>
    <div class="success-message">Payment completed successfully!</div>
<?php endif; ?>

<div class="payments-container">
    <div class="page-header">
        <h1>Payments</h1>
        <div class="total-spent">
            <span>Total Spent:</span>
            <strong>$<?= number_format($total_spent, 2) ?></strong>
        </div>
    </div>

    <?php if ($pending_query && $pending_query->num_rows > 0): ?>
        <div class="section">
            <h2>Pending Payments</h2>
            <div class="pending-payments-list">
                <?php while ($pending = $pending_query->fetch_assoc()): ?>
                    <div class="payment-card pending-card">
                        <div class="payment-info">
                            <h3><?= htmlspecialchars($pending['job_title']) ?></h3>
                            <p class="freelancer-name">Freelancer: <?= htmlspecialchars($pending['freelancer_name']) ?></p>
                            <span class="amount">$<?= number_format($pending['budget'], 2) ?></span>
                        </div>
                        <form method="POST" action="actions/make_payment.php" class="payment-form">
                            <input type="hidden" name="hire_id" value="<?= $pending['hire_id'] ?>">
                            <input type="number" name="amount" step="0.01" value="<?= $pending['budget'] ?>" 
                                   min="1" required class="amount-input">
                            <button type="submit" class="btn-pay" 
                                    onclick="return confirm('Confirm payment of $' + this.form.amount.value + '?')">
                                Make Payment
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Payment History</h2>
        <?php if ($payments_query && $payments_query->num_rows > 0): ?>
            <div class="payments-table-wrapper">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Job Title</th>
                            <th>Freelancer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments_query->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($payment['paid_at'])) ?></td>
                                <td><?= htmlspecialchars($payment['job_title']) ?></td>
                                <td><?= htmlspecialchars($payment['freelancer_name']) ?></td>
                                <td class="amount-cell">$<?= number_format($payment['amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge <?= $payment['payment_status'] ?>">
                                        <?= ucfirst($payment['payment_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No payment history yet</p>
        <?php endif; ?>
    </div>
</div>