<?php 
include 'header.php'; 
?>

<link rel="stylesheet" href="css/login.css">

<main class="login-page">

    <div class="admin-link">
        <a href="admin/adminLogin.php">Login As Admin</a>
    </div>

    <h1 class="login-title">Log in to your account</h1>

    <div class="login-card">

        <form action="#" method="post">

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

    <a href="#" class="forgot-password">Forgot password?</a>

</main>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

<?php 
include 'footer.php'; 
?>
