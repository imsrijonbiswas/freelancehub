<?php
$pageCSS = 'admin/adminSignup.css';
include '../header.php';
?>

<div class="admin-signup-container">
    <div class="admin-signup-content">
        <h1>Sign Up as an Admin</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-popup">Signup successful! You can now log in.</div>
        <?php endif; ?>

        <form class="admin-signup-form" method="POST" action="process_admin_signup.php">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group password-group">
                <input type="password" name="password" id="admin_password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('admin_password')">
                    👁
                </button>
            </div>

            <div class="form-group password-group">
                <input type="password" name="confirm_password" id="admin_confirm_password" placeholder="Confirm Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('admin_confirm_password')">
                    👁
                </button>
            </div>

            <button type="submit" class="btn-admin-continue">Continue</button>
        </form>

        <p class="admin-login-link">
            Already have an account? <a href="adminLogin.php">Log In</a>
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
