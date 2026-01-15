<?php
// FreelanceHub/admin/dashboard.php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: adminlogin.php');
    exit();
}

require '../db_config.php';

// Get current page
$page = $_GET['page'] ?? 'dashboard';

// Define available pages
$pages = [
    'dashboard' => 'Dashboard',
    'users'     => 'Users',
    'skills'    => 'Skills',
    'quizzes'   => 'Quiz Bank',
    'courses'   => 'Courses'
];

$pageTitle = $pages[$page] ?? 'Dashboard';
$pageFile  = $page . '.php';

// Get admin info
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT full_name FROM admins WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin['full_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - FreelanceHub Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <?php if ($page == 'dashboard'): ?>
        <link rel="stylesheet" href="css/dashboard.css">
    <?php elseif ($page == 'quizzes'): ?>
        <link rel="stylesheet" href="css/quizzes.css">
    <?php elseif ($page == 'courses'): ?>
        <link rel="stylesheet" href="css/courses.css">
    <?php endif; ?>
</head>
<body>
    <div class="admin-container">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="../index.php" class="logo-link">
                        <span class="logo-icon">◐</span>
                        <span class="logo-text">FreelanceHub</span>
                    </a>
                </div>
                
                <div class="admin-profile">
                    <div class="admin-avatar">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <circle cx="20" cy="20" r="20" fill="#e0e0e0"/>
                            <path d="M20 20c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2.5c-3.33 0-10 1.67-10 5v2.5h20v-2.5c0-3.33-6.67-5-10-5z" fill="#999"/>
                        </svg>
                    </div>
                    <span class="admin-name">Admin</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <?php foreach ($pages as $key => $label): ?>
                    <a href="dashboard.php?page=<?= $key ?>" 
                       class="nav-item <?= $page === $key ? 'active' : '' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <div class="sidebar-footer">
                <a href="adminLogout.php" class="logout-link">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M6 14H4c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h2v2H4v8h2v2zm6.6-6L9 4.4 10.4 3l5.6 5.6-5.6 5.6L9 12.6 12.6 9H6V7h6.6z"/>
                    </svg>
                    Log Out
                </a>
            </div>
        </aside>
        
        <!-- MAIN CONTENT -->
        <main class="admin-main">
            <div class="main-header">
                <h1><?= htmlspecialchars($pageTitle) ?></h1>
            </div>
            
            <div class="main-content">
                <?php
                $path = __DIR__ . '/pages/' . $pageFile;
                if (file_exists($path)) {
                    include $path;
                } else {
                    echo '<div class="error-message">Page not found</div>';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>