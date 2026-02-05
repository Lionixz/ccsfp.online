<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../handlers/google_auth.php';

// Google login URL
$loginUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Student Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/system/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-container">
        <!-- LEFT SIDE -->
        <div class="left-side">
            <img src="../images/system/ccsfp-building.jpg" alt="building" class="bg-img">
            <div class="overlay-text">
                <h1>Vision</h1>
                <p>To be a leading institution of academic excellence that provides accessible and quality tertiary education</p>
            </div>
            <img src="../images/system/logo.png" alt="Logo" class="top-img">
        </div>

        <!-- RIGHT SIDE -->
        <div class="right-side">
            <div class="login-form">
                <h2>Register Student Account</h2>

                <!-- FAKE FORM -->
                <form id="registerForm">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <label>Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <label>Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <button type="submit" class="btn-login">Register</button>
                </form>

                <div class="divider">OR</div>

                <!-- Google login -->
                <a id="googleLoginBtn" href="<?= htmlspecialchars($loginUrl) ?>" class="btn-google">
                    Login with Google
                </a>

                <p class="info-text">
                    Please login using your Google account to access your student portal.
                </p>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const googleUrl = document.getElementById('googleLoginBtn').href;

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // prevent normal form submission

            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            // Gmail validation
            const gmailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            if (!gmailPattern.test(email)) {
                alert("Please enter a valid Gmail address (example@gmail.com).");
                return;
            }

            // Password validation
            const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 8 characters long and include both letters and numbers.");
                return;
            }

            // Confirm password
            if (password !== confirm) {
                alert("Passwords do not match!");
                return;
            }

            // ✅ Validation passed → redirect to Google login
            window.location.href = googleUrl;
        });
    </script>
</body>

</html>