<?php
// adminlogin.php
$pageCSS = 'admin/adminLogin.css';
include '../header.php';
?>

<div class="login-container">
    <div class="login-content">
        <h1>Admin Log in</h1>
        
        <form class="login-form" method="POST" action="process_admin_login.php">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group password-group">
                <input type="password" name="password" id="admin_password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('admin_password')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                        <line x1="1" y1="1" x2="23" y2="23" class="eye-slash"></line>
                    </svg>
                </button>
            </div>
            
            <button type="submit" class="btn-login">Log in</button>
        </form>
        
        <p class="forgot-link">
            <a href="forgot_password.php">Forgot password?</a>
        </p>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const slash = button.querySelector('.eye-slash');
    
    if (field.type === 'password') {
        field.type = 'text';
        slash.style.display = 'none';
    } else {
        field.type = 'password';
        slash.style.display = 'block';
    }
}
</script>

<?php
include '../footer.php';
?>