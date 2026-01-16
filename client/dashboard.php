<?php
// FreelanceHub/client/dashboard.php
session_start();

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: ../login.php');
    exit();
}

require '../db_config.php';

// Get current page
$page = $_GET['page'] ?? 'home';

// Define available pages
$pages = [
    'home' => 'Dashboard',
    'post-job' => 'Post Job',
    'jobs' => 'My Jobs',
    'freelancers' => 'Find Freelancers',
    'messages' => 'Messages',
    'payments' => 'Payments',
    'settings' => 'Settings'
];

$pageTitle = $pages[$page] ?? 'Dashboard';

// Get client info
$client_id = $_SESSION['client_id'];
$client_name = $_SESSION['client_name'];

// Get unread messages count (placeholder for now)
$unread_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - FreelanceHub Client</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/client_dashboard.css">
</head>
<body>
    <div class="client-layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="../index.php" class="logo-link">
                        <span class="logo-icon">◐</span>
                        <span class="logo-text">FreelanceHub</span>
                    </a>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h3><?= htmlspecialchars(explode(' ', $client_name)[0]) ?></h3>
            </div>

            <nav class="sidebar-nav">
                <?php foreach ($pages as $key => $label): ?>
                    <a href="dashboard.php?page=<?= $key ?>" 
                       class="<?= $page === $key ? 'active' : '' ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <?php if ($key == 'home'): ?>
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            <?php elseif ($key == 'post-job'): ?>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            <?php elseif ($key == 'jobs'): ?>
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            <?php elseif ($key == 'freelancers'): ?>
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            <?php elseif ($key == 'messages'): ?>
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            <?php elseif ($key == 'payments'): ?>
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            <?php else: ?>
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M12 1v6m0 6v6m9-9h-6m-6 0H3"></path>
                            <?php endif; ?>
                        </svg>
                        <?= htmlspecialchars($label) ?>
                        <?php if ($key === 'messages' && $unread_count > 0): ?>
                            <span class="badge"><?= $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="logout">
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
            <?php
            $pageFile = $page . '.php';
            $path = __DIR__ . '/pages/' . $pageFile;
            if (file_exists($path)) {
                include $path;
            } else {
                echo '<div class="info-message">This page is under construction.</div>';
            }
            ?>
        </main>
    </div>
</body>
</html>