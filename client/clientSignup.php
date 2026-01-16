<?php
// FreelanceHub/client/clientSignup.php
session_start();
require '../db_config.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

$pageCSS = 'client/clientSignup.css';
include '../header.php';
?>

<?php if ($error): ?>
    <div style="max-width: 480px; margin: 20px auto; padding: 15px; background: #fee; color: #c33; border-radius: 8px; border-left: 4px solid #e53935;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="max-width: 480px; margin: 20px auto; padding: 15px; background: #e8f5e9; color: #2e7d32; border-radius: 8px; border-left: 4px solid #4caf50;">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="signup-container">
    <div class="signup-content">
        <h1>Sign Up as a Client</h1>
        <p class="subtitle">Create an account to post jobs<br>and collaborate with freelancers</p>
        
        <form class="signup-form" method="POST" action="process_client_signup.php">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password" required minlength="6">
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                        <line x1="1" y1="1" x2="23" y2="23" class="eye-slash"></line>
                    </svg>
                </button>
            </div>
            
            <div class="form-group password-group">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                        <line x1="1" y1="1" x2="23" y2="23" class="eye-slash"></line>
                    </svg>
                </button>
            </div>
            
            <button type="submit" class="btn-continue">Continue</button>
        </form>
        
        <p class="login-link">
            Already have an account? <a href="../login.php">Log In</a>
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

<?php include '../footer.php'; ?>