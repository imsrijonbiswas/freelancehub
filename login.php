<?php
// FreelanceHub/login.php
$error = $_GET['error'] ?? '';

$pageCSS = 'css/login.css';
include 'header.php';
?>

<?php if ($error): ?>
    <div style="max-width: 500px; margin: 20px auto; padding: 15px; background: #fee; color: #c33; border-radius: 8px; border-left: 4px solid #e53935; text-align: center;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<main class="login-page">
    <div class="admin-link">
        <a href="admin/adminLogin.php">Login As Admin</a>
    </div>
    <h1 class="login-title">Log in to your account</h1>
    <div class="login-card">
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁</span>
            </div>
            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
    <div class="signup-links">
        <p>Don't have an account?</p>
        <a href="freelancer/freelancerSignup.php" class="signup-link">Sign Up as Freelancer</a>
        <span> | </span>
        <a href="client/clientSignup.php" class="signup-link">Sign Up as Client</a>
    </div>
</main>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

<?php include 'footer.php'; ?>