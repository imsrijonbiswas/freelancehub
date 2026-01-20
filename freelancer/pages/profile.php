<?php
// FreelanceHub/freelancer/pages/profile.php

$freelancer_id = $_SESSION['freelancer_id'];

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

<link rel="stylesheet" href="css/update_profile.css">