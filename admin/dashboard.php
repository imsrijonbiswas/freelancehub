<?php
require 'admin_auth_check.php';
require '../db_config.php';

$pageCSS = 'admin/css/admin.css';
include '../header.php';

// determine which page to load
$page = $_GET['page'] ?? 'dashboard';

$allowed_pages = [
    'dashboard' => 'dashboard_home.php',
    'users'     => 'users.php',
    'quizzes'   => 'quiz_bank.php',
    'courses'   => 'courses.php'
];

$page_file = $allowed_pages[$page] ?? 'dashboard_home.php';
?>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="brand">FreelanceHub</div>

        <div class="admin-profile">
            <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong>
        </div>

        <nav>
            <a href="dashboard.php?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
            <a href="dashboard.php?page=users" class="<?= $page=='users'?'active':'' ?>">Users</a>
            <a href="dashboard.php?page=quizzes" class="<?= $page=='quizzes'?'active':'' ?>">Quizzes</a>
            <a href="dashboard.php?page=courses" class="<?= $page=='courses'?'active':'' ?>">Courses</a>
        </nav>

        <a href="adminLogout.php" class="logout">Logout</a>
    </aside>

    <!-- CONTENT -->
    <main class="content">
        <?php include __DIR__ . '/pages/' . $page_file; ?>
    </main>

</div>

<?php include '../footer.php'; ?>
