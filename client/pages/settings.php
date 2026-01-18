<?php
// FreelanceHub/client/pages/settings.php

$client_id = $_SESSION['client_id'];


$profile_query = $conn->prepare("SELECT * FROM clients WHERE client_id = ?");
$profile_query->bind_param("i", $client_id);
$profile_query->execute();
$profile = $profile_query->get_result()->fetch_assoc();

$message = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<link rel="stylesheet" href="css/settings.css">

<?php if ($message == 'updated'): ?>
    <div class="success-message">Profile updated successfully!</div>
<?php elseif ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="settings-container">
    <h1>Account Settings</h1>
    
    <div class="settings-grid">
        <div class="settings-card">
            <h2>Profile Information</h2>
            <form method="POST" action="actions/update_client_profile.php" class="settings-form">
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
                
                <button type="submit" class="btn-update">Update Profile</button>
            </form>
        </div>
        
        <div class="settings-card">
            <h2>Change Password</h2>
            <form method="POST" action="actions/change_client_password.php" class="settings-form">
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
</div>