<?php
// FreelanceHub/admin/pages/users.php

$filter_type = $_GET['type'] ?? 'all';
$filter_status = $_GET['status'] ?? 'active';

$total_freelancers = $conn->query("SELECT COUNT(*) as total FROM freelancers WHERE is_suspended = FALSE")->fetch_assoc()['total'] ?? 0;
$total_clients = $conn->query("SELECT COUNT(*) as total FROM clients WHERE is_suspended = FALSE")->fetch_assoc()['total'] ?? 0;
$suspended_freelancers = $conn->query("SELECT COUNT(*) as total FROM freelancers WHERE is_suspended = TRUE")->fetch_assoc()['total'] ?? 0;
$suspended_clients = $conn->query("SELECT COUNT(*) as total FROM clients WHERE is_suspended = TRUE")->fetch_assoc()['total'] ?? 0;

$suspend_condition = $filter_status == 'suspended' ? 'TRUE' : 'FALSE';

if ($filter_type == 'freelancers') {
    $users_query = $conn->query("
        SELECT 
            f.freelancer_id as user_id,
            f.full_name,
            f.email,
            f.created_at,
            f.is_suspended,
            'freelancer' as user_type,
            (SELECT COUNT(*) FROM user_reports 
             WHERE reported_user_id = f.freelancer_id 
             AND reported_user_type = 'freelancer') as report_count
        FROM freelancers f
        WHERE f.is_suspended = $suspend_condition
        ORDER BY f.created_at DESC
    ");
} elseif ($filter_type == 'clients') {
    $users_query = $conn->query("
        SELECT 
            c.client_id as user_id,
            c.full_name,
            c.email,
            c.created_at,
            c.is_suspended,
            'client' as user_type,
            (SELECT COUNT(*) FROM user_reports 
             WHERE reported_user_id = c.client_id 
             AND reported_user_type = 'client') as report_count
        FROM clients c
        WHERE c.is_suspended = $suspend_condition
        ORDER BY c.created_at DESC
    ");
} else {
    $users_query = $conn->query("
        SELECT 
            f.freelancer_id as user_id,
            f.full_name,
            f.email,
            f.created_at,
            f.is_suspended,
            'freelancer' as user_type,
            (SELECT COUNT(*) FROM user_reports 
             WHERE reported_user_id = f.freelancer_id 
             AND reported_user_type = 'freelancer') as report_count
        FROM freelancers f
        WHERE f.is_suspended = $suspend_condition
        UNION ALL
        SELECT 
            c.client_id as user_id,
            c.full_name,
            c.email,
            c.created_at,
            c.is_suspended,
            'client' as user_type,
            (SELECT COUNT(*) FROM user_reports 
             WHERE reported_user_id = c.client_id 
             AND reported_user_type = 'client') as report_count
        FROM clients c
        WHERE c.is_suspended = $suspend_condition
        ORDER BY created_at DESC
    ");
}

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($message == 'suspended'): ?>
    <div class="success-message">User suspended successfully!</div>
<?php elseif ($message == 'activated'): ?>
    <div class="success-message">User activated successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="users-stats">
    <div class="stat-card-small">
        <span class="stat-number-small"><?= $total_freelancers ?></span>
        <span class="stat-label-small">Active Freelancers</span>
    </div>
    <div class="stat-card-small">
        <span class="stat-number-small"><?= $total_clients ?></span>
        <span class="stat-label-small">Active Clients</span>
    </div>
    <div class="stat-card-small suspended">
        <span class="stat-number-small"><?= $suspended_freelancers + $suspended_clients ?></span>
        <span class="stat-label-small">Suspended Users</span>
    </div>
</div>

<div class="users-section">
    <div class="section-header">
        <h2>Manage Users</h2>
        <div class="filter-container">
            <div class="filter-buttons">
                <a href="dashboard.php?page=users&type=all&status=<?= $filter_status ?>" 
                   class="filter-btn <?= $filter_type == 'all' ? 'active' : '' ?>">
                    All Users
                </a>
                <a href="dashboard.php?page=users&type=freelancers&status=<?= $filter_status ?>" 
                   class="filter-btn <?= $filter_type == 'freelancers' ? 'active' : '' ?>">
                    Freelancers
                </a>
                <a href="dashboard.php?page=users&type=clients&status=<?= $filter_status ?>" 
                   class="filter-btn <?= $filter_type == 'clients' ? 'active' : '' ?>">
                    Clients
                </a>
            </div>
            <div class="status-buttons">
                <a href="dashboard.php?page=users&type=<?= $filter_type ?>&status=active" 
                   class="status-btn <?= $filter_status == 'active' ? 'active' : '' ?>">
                    Active
                </a>
                <a href="dashboard.php?page=users&type=<?= $filter_type ?>&status=suspended" 
                   class="status-btn <?= $filter_status == 'suspended' ? 'active' : '' ?>">
                    Suspended
                </a>
            </div>
        </div>
    </div>
    
    <?php if ($users_query && $users_query->num_rows > 0): ?>
        <div class="users-table-wrapper">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Joined Date</th>
                        <th>Reports</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users_query->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['user_id'] ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="user-type-badge <?= strtolower($user['user_type']) ?>">
                                    <?= ucfirst($user['user_type']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if ($user['report_count'] > 0): ?>
                                    <span class="report-count warning"><?= $user['report_count'] ?></span>
                                <?php else: ?>
                                    <span class="report-count clean">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_suspended']): ?>
                                    <span class="status-badge suspended">Suspended</span>
                                <?php else: ?>
                                    <span class="status-badge active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['is_suspended']): ?>
                                    <a href="actions/toggle_suspend.php?id=<?= $user['user_id'] ?>&type=<?= $user['user_type'] ?>&action=activate" 
                                       class="btn-success btn-small"
                                       onclick="return confirm('Activate this user account?')">
                                        Activate
                                    </a>
                                <?php else: ?>
                                    <a href="actions/toggle_suspend.php?id=<?= $user['user_id'] ?>&type=<?= $user['user_type'] ?>&action=suspend" 
                                       class="btn-danger btn-small"
                                       onclick="return confirm('Suspend this user account? They will not be able to login.')">
                                        Suspend
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                <circle cx="32" cy="32" r="32" fill="#f0f0f0"/>
                <path d="M32 20v24M20 32h24" stroke="#ccc" stroke-width="3" stroke-linecap="round"/>
            </svg>
            <p>No users found</p>
        </div>
    <?php endif; ?>
</div>