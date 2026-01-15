<?php
// FreelanceHub/client/dashboard.php
session_start();

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit();
}

$pageCSS = 'client/css/client_dashboard.css';
include '../header.php';
include '../db_config.php';

$clientId = $_SESSION['client_id'];

// Fetch client info
$stmt = $conn->prepare("SELECT name, email FROM clients WHERE client_id = ?");
$stmt->bind_param("i", $clientId);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();
$stmt->close();

$page = $_GET['page'] ?? 'dashboard';

$allowed = [
    'dashboard',
    'post-job',
    'jobs',
    'freelancers',
    'messages',
    'payments',
    'settings'
];

if (!in_array($page, $allowed)) {
    $page = 'dashboard';
}
?>

<div class="client-layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 6v6l4 2"></path>
                </svg>
                <span>FreelanceHub</span>
            </div>
        </div>
        
        <div class="user-profile">
            <div class="avatar">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <h3><?= htmlspecialchars($client['name'] ?? 'Mr. X') ?></h3>
        </div>

        <nav class="sidebar-nav">
            <a href="?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>
            <a href="?page=post-job" class="<?= $page === 'post-job' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Post Jobs
            </a>
            <a href="?page=jobs" class="<?= $page === 'jobs' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
                My Jobs
            </a>
            <a href="?page=freelancers" class="<?= $page === 'freelancers' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Freelancers
            </a>
            <a href="?page=messages" class="<?= $page === 'messages' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Messages
            </a>
            <a href="?page=payments" class="<?= $page === 'payments' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                    <line x1="1" y1="10" x2="23" y2="10"></line>
                </svg>
                Payments
            </a>
            <a href="?page=settings" class="<?= $page === 'settings' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"></path>
                </svg>
                Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../logout.php" class="logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Log Out
            </a>
        </div>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <?php include "pages/$page.php"; ?>
    </main>
</div>

<?php include '../footer.php'; ?>