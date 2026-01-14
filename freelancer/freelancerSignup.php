<?php
$pageCSS = 'freelancer/freelancerSignup.css';
include '../header.php';
?>


<div class="signup-container">
    <div class="signup-content">
        <h1>Sign Up as a Freelancer</h1>
        <p class="subtitle">Create a profile and get verified with a skill test</p>
        
        <form class="signup-form" method="POST" action="freelancerTestrules.php">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            
            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
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
            
            <div class="form-group">
                <select name="skill_category" required>
                    <option value="" disabled selected>Select Your Skill Category</option>
                    <option value="web-development">Web Development</option>
                    <option value="mobile-development">Mobile Development</option>
                    <option value="graphic-design">Graphic Design</option>
                    <option value="content-writing">Content Writing</option>
                    <option value="digital-marketing">Digital Marketing</option>
                    <option value="video-editing">Video Editing</option>
                    <option value="data-entry">Data Entry</option>
                    <option value="virtual-assistant">Virtual Assistant</option>
                </select>
            </div>
            
            <button type="submit" class="btn-continue">
                Continue
                <span class="arrow">→</span>
                <span class="skill-test-text">Take Skill Test</span>
            </button>
        </form>
        
        <p class="login-link">
            Already have an account? <a href="login.php">Log In</a>
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
