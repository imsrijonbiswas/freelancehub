<?php
// FreelanceHub/admin/pages/users.php

// Get filter
$filter_type = $_GET['type'] ?? 'all'; // all, freelancers, clients

// Get statistics
$total_freelancers = $conn->query("SELECT COUNT(*) as total FROM freelancers")->fetch_assoc()['total'] ?? 0;
$total_clients = $conn->query("SELECT COUNT(*) as total FROM clients")->fetch_assoc()['total'] ?? 0;

// Get users based on filter
if ($filter_type == 'freelancers') {
    $users_query = $conn->query("
        SELECT 
            freelancer_id as user_id,
            full_name,
            email,
            created_at,
            'Freelancer' as user_type
        FROM freelancers
        ORDER BY created_at DESC
    ");
} elseif ($filter_type == 'clients') {
    $users_query = $conn->query("
        SELECT 
            client_id as user_id,
            full_name,
            email,
            created_at,
            'Client' as user_type
        FROM clients
        ORDER BY created_at DESC
    ");
} else {
    // All users
    $users_query = $conn->query("
        SELECT 
            freelancer_id as user_id,
            full_name,
            email,
            created_at,
            'Freelancer' as user_type
        FROM freelancers
        UNION ALL
        SELECT 
            client_id as user_id,
            full_name,
            email,
            created_at,
            'Client' as user_type
        FROM clients
        ORDER BY created_at DESC
    ");
}

// Get messages
$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!-- Messages -->
<?php if ($message == 'deleted'): ?>
    <div class="success-message">User deleted successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Statistics Section -->
<div class="users-stats">
    <div class="stat-card-small">
        <span class="stat-number-small"><?= $total_freelancers ?></span>
        <span class="stat-label-small">Freelancers</span>
    </div>
    <div class="stat-card-small">
        <span class="stat-number-small"><?= $total_clients ?></span>
        <span class="stat-label-small">Clients</span>
    </div>
    <div class="stat-card-small">
        <span class="stat-number-small"><?= $total_freelancers + $total_clients ?></span>
        <span class="stat-label-small">Total Users</span>
    </div>
</div>

<!-- Users List Section -->
<div class="users-section">
    <div class="section-header">
        <h2>All Users</h2>
        <div class="filter-buttons">
            <a href="dashboard.php?page=users&type=all" 
               class="filter-btn <?= $filter_type == 'all' ? 'active' : '' ?>">
                All Users
            </a>
            <a href="dashboard.php?page=users&type=freelancers" 
               class="filter-btn <?= $filter_type == 'freelancers' ? 'active' : '' ?>">
                Freelancers
            </a>
            <a href="dashboard.php?page=users&type=clients" 
               class="filter-btn <?= $filter_type == 'clients' ? 'active' : '' ?>">
                Clients
            </a>
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
                                    <?= $user['user_type'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <a href="actions/delete_user.php?id=<?= $user['user_id'] ?>&type=<?= strtolower($user['user_type']) ?>" 
                                   class="btn-danger btn-small"
                                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    Delete
                                </a>
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