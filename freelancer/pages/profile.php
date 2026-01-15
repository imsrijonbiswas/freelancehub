<?php
// FreelanceHub/freelancer/pages/profile.php

$freelancer_id = $_SESSION['freelancer_id'];

// Get freelancer details
$profile_query = $conn->prepare("
    SELECT f.*, s.skill_name 
    FROM freelancers f
    LEFT JOIN skill_categories s ON f.skill_id = s.skill_id
    WHERE f.freelancer_id = ?
");
$profile_query->bind_param("i", $freelancer_id);
$profile_query->execute();
$profile = $profile_query->get_result()->fetch_assoc();

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($message == 'updated'): ?>
    <div class="success-message">Profile updated successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="profile-container">
    <div class="profile-card">
        <h2>Personal Information</h2>
        <form method="POST" action="actions/update_profile.php" class="profile-form">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?= htmlspecialchars($profile['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($profile['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4" 
                          placeholder="Tell clients about yourself..."><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Skill Category</label>
                <input type="text" value="<?= htmlspecialchars($profile['skill_name']) ?>" disabled>
                <small>Contact admin to change your skill category</small>
            </div>
            
            <button type="submit" class="btn-update">Update Profile</button>
        </form>
    </div>
    
    <div class="profile-card">
        <h2>Change Password</h2>
        <form method="POST" action="actions/change_password.php" class="profile-form">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-update">Change Password</button>
        </form>
    </div>
</div>

<style>
.profile-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    padding: 20px 0;
}

.profile-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.profile-card h2 {
    margin: 0 0 25px 0;
    font-size: 1.3rem;
    color: #1a1a1a;
}

.profile-form .form-group {
    margin-bottom: 20px;
}

.profile-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 0.9rem;
}

.profile-form input,
.profile-form textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.2s ease;
}

.profile-form input:focus,
.profile-form textarea:focus {
    outline: none;
    border-color: #00a2ff;
    box-shadow: 0 0 0 3px rgba(0, 162, 255, 0.1);
}

.profile-form input:disabled {
    background: #f8f9fa;
    color: #666;
    cursor: not-allowed;
}

.profile-form small {
    display: block;
    margin-top: 5px;
    font-size: 0.8rem;
    color: #999;
}

.btn-update {
    width: 100%;
    padding: 12px 24px;
    background: #00a2ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-update:hover {
    background: #0090e0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 162, 255, 0.3);
}

@media (max-width: 768px) {
    .profile-container {
        grid-template-columns: 1fr;
    }
}
</style>