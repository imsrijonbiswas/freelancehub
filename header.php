<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FreelanceHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/FreelanceHub/css/style.css">

    <?php if (isset($pageCSS)): ?>
        <link rel="stylesheet" href="/FreelanceHub/<?= $pageCSS ?>">
    <?php endif; ?>
</head>
<body>

<header>
    <div class="navbar">
        <div class="logo">
            <a href="/FreelanceHub/index.php" class="logo-link">
                <span class="logo-icon">◐</span>
                <span class="logo-text">FreelanceHub</span>
            </a>
        </div>

        <nav>
            <ul>
                <li><a href="#">Browse Skills</a></li>
                <li><a href="#">Jobs</a></li>
                <li><a href="#">Freelancers</a></li>
                <li><a href="#">Prices</a></li>
                <li><a href="#">About</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <a href="/FreelanceHub/login.php" class="btn login">Login</a>
            <a href="/FreelanceHub/signupOpt.php" class="btn signup">Sign Up</a>
        </div>
    </div>
</header>
