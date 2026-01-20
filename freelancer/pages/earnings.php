<?php
// FreelanceHub/freelancer/pages/earnings.php

$freelancer_id = $_SESSION['freelancer_id'];

$earnings_query = $conn->query("
    SELECT 
        p.*,
        hf.job_id,
        j.job_title,
        c.full_name as client_name
    FROM payments p
    JOIN hired_freelancers hf ON p.hire_id = hf.hire_id
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN clients c ON hf.client_id = c.client_id
    WHERE hf.freelancer_id = $freelancer_id
    ORDER BY p.paid_at DESC
");

$total_query = $conn->query("
    SELECT COALESCE(SUM(p.amount), 0) as total
    FROM payments p
    JOIN hired_freelancers hf ON p.hire_id = hf.hire_id
    WHERE hf.freelancer_id = $freelancer_id AND p.payment_status = 'completed'
");
$total_earned = $total_query->fetch_assoc()['total'];

$pending_query = $conn->query("
    SELECT 
        hf.hire_id,
        hf.job_id,
        j.job_title,
        j.budget,
        c.full_name as client_name,
        hf.hired_at
    FROM hired_freelancers hf
    JOIN jobs j ON hf.job_id = j.job_id
    JOIN clients c ON hf.client_id = c.client_id
    LEFT JOIN payments p ON hf.hire_id = p.hire_id
    WHERE hf.freelancer_id = $freelancer_id 
    AND hf.status = 'active'
    AND p.payment_id IS NULL
    ORDER BY hf.hired_at DESC
");

$message = $_GET['msg'] ?? '';
?>

<link rel="stylesheet" href="css/earnings.css">

<?php if ($message == 'success'): ?>
    <div class="success-message">Payment information updated!</div>
<?php endif; ?>

<div class="earnings-container">
    <div class="page-header">
        <h1>My Earnings</h1>
        <div class="total-earned">
            <span>Total Earned:</span>
            <strong>$<?= number_format($total_earned, 2) ?></strong>
        </div>
    </div>

    <?php if ($pending_query && $pending_query->num_rows > 0): ?>
        <div class="section">
            <h2>Pending Payments</h2>
            <div class="pending-earnings-list">
                <?php while ($pending = $pending_query->fetch_assoc()): ?>
                    <div class="earning-card pending-card">
                        <div class="earning-info">
                            <h3><?= htmlspecialchars($pending['job_title']) ?></h3>
                            <p class="client-name">Client: <?= htmlspecialchars($pending['client_name']) ?></p>
                            <span class="pending-amount">$<?= number_format($pending['budget'], 2) ?></span>
                        </div>
                        <div class="pending-status">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 6v6l4 2"></path>
                            </svg>
                            <span>Awaiting Payment</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="section">
        <h2>Payment History</h2>
        <?php if ($earnings_query && $earnings_query->num_rows > 0): ?>
            <div class="earnings-table-wrapper">
                <table class="earnings-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Job Title</th>
                            <th>Client</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($earning = $earnings_query->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($earning['paid_at'])) ?></td>
                                <td><?= htmlspecialchars($earning['job_title']) ?></td>
                                <td><?= htmlspecialchars($earning['client_name']) ?></td>
                                <td class="amount-cell">$<?= number_format($earning['amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge <?= $earning['payment_status'] ?>">
                                        <?= ucfirst($earning['payment_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                    <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                    <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
                </svg>
                <p>No payment history yet</p>
                <p class="subtext">Complete jobs to start earning!</p>
            </div>
        <?php endif; ?>
    </div>
</div>