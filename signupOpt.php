<?php
$pageCSS = 'css/signupOpt.css';
include 'header.php';
?>

<main class="signup-page">
    <section class="signup-options">
        <h1>How do you want to join?</h1>

        <div class="cards-container">
            <!-- Client Card -->
            <div class="signup-card client-card">
                <div class="card-icon">💼</div> <!-- optional, can replace with img -->
                <h2>Hire for a Project</h2>
                <ul>
                    <li>Post jobs</li>
                    <li>View verified freelancers</li>
                    <li>Hire based on test scores</li>
                    <li>Manage projects easily</li>
                </ul>
                <a href="signup_client.php" class="btn-card">Join as Client</a>
            </div>

            <!-- Freelancer Card -->
            <div class="signup-card freelancer-card">
                <div class="card-icon">🧑‍💻</div> <!-- optional, can replace with img -->
                <h2>Work as a Freelancer</h2>
                <ul>
                    <li>Create a skill profile</li>
                    <li>Take skill verification tests</li>
                    <li>Get job recommendations</li>
                    <li>Earn verified badges</li>
                </ul>
                <a href="freelancer/freelancerSignup.php" class="btn-card">Join as Freelancer</a>
            </div>
        </div>

        <p class="already-account">
            Already have an account? <a href="login.php">Log In</a>
        </p>
    </section>
</main>

<?php
include 'footer.php';
?>
