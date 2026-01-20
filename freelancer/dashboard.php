<?php
// FreelanceHub/freelancer/dashboard.php
session_name("FREELANCER_SESSION");
session_start();

if (!isset($_SESSION['freelancer_id'])) {
    header('Location: ../login.php');
    exit();
}

require '../db_config.php';

$page = $_GET['page'] ?? 'home';

$pages = [
    'home' => 'Dashboard',
    'jobs' => 'Browse Jobs',
    'applications' => 'My Applications',
    'earnings' => 'My Earnings',
    'messages' => 'Messages',
    'profile' => 'My Profile'
];

$pageTitle = $pages[$page] ?? 'Dashboard';

$freelancer_id = $_SESSION['freelancer_id'];
$freelancer_name = $_SESSION['freelancer_name'];
$freelancer_skill = $_SESSION['freelancer_skill_name'];

$unread_query = $conn->query("
    SELECT COUNT(DISTINCT m.job_id) as unread_count
    FROM messages m
    JOIN hired_freelancers hf ON m.job_id = hf.job_id
    WHERE hf.freelancer_id = $freelancer_id 
    AND m.sender_type = 'client'
    AND m.message_id > COALESCE((
        SELECT MAX(message_id) FROM messages 
        WHERE job_id = m.job_id 
        AND sender_type = 'freelancer'
    ), 0)
");
$unread_count = $unread_query->fetch_assoc()['unread_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - FreelanceHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/freelancer_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="dashboard-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="../index.php" class="logo-link">
                        <span class="logo-icon">◐</span>
                        <span class="logo-text">FreelanceHub</span>
                    </a>
                </div>
                
                <div class="freelancer-profile">
                    <div class="freelancer-avatar">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="20" fill="#e3f2fd"/>
                            <path d="M20 20c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2.5c-3.33 0-10 1.67-10 5v2.5h20v-2.5c0-3.33-6.67-5-10-5z" fill="#1976d2"/>
                        </svg>
                    </div>
                    <span class="freelancer-name"><?= htmlspecialchars(explode(' ', $freelancer_name)[0]) ?></span>
                    <span class="freelancer-skill"><?= htmlspecialchars($freelancer_skill) ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <?php foreach ($pages as $key => $label): ?>
                    <a href="dashboard.php?page=<?= $key ?>" 
                       class="nav-item <?= $page === $key ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?>
                        <!-- <?php if ($key === 'messages' && $unread_count > 0): ?>
                            <span class="badge"><?= $unread_count ?></span>
                        <?php endif; ?> -->
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-link">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6 14H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h2v2H4v8h2v2zm6.6-6L9 4.4 10.4 3l5.6 5.6-5.6 5.6L9 12.6 12.6 9H6V7h6.6z"/>
                    </svg>
                    Log Out
                </a>
            </div>
        </aside>
        
        <main class="dashboard-main">
            <div class="main-header">
                <h1><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            
            <div class="main-content">
                <?php
                $pageFile = $page . '.php';
                $path = __DIR__ . '/pages/' . $pageFile;
                if (file_exists($path)) {
                    include $path;
                } else {
                    echo '<div class="info-message">This page is under construction.</div>';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>