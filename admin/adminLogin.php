<?php
$pageCSS = 'admin/css/adminLogin.css';
include '../header.php';
?>

<div class="login-container">
    <div class="login-content">
        <h1>Admin Log in</h1>

        <?php if (isset($_GET['error'])): ?>
            <p class="error-msg">Invalid email or password</p>
        <?php endif; ?>

        <form class="login-form" method="POST" action="process_admin_login.php">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group password-group">
                <input type="password" name="password" id="admin_password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('admin_password')">
                    👁
                </button>
            </div>

            <button type="submit" class="btn-login">Log in</button>
        </form>

        <p class="forgot-link">
            <a href="#">Forgot password?</a>
        </p>
    </div>
</div>

<script>
function togglePassword(id) {
    const field = document.getElementById(id);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>

<?php include '../footer.php'; ?>
