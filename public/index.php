


<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../handlers/google_auth.php';

// --- CSRF protection for Google OAuth ---
$state = bin2hex(random_bytes(16));
$_SESSION['google_auth_state'] = $state;
$client->setState($state);
// ---

// Google login URL
$loginUrl = $client->createAuthUrl();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register Student Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <link rel="icon" href="../public/images/system/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
   
</head>

<body>
    <div class="login-container">
        <div class="left-side">
            <img src="../public/images/system/ccsfp-building.jpg" alt="building" class="bg-img">

            <!-- Logo and School Name Container -->
            <div class="logo-container">
                <img src="../public/images/system/logo.png" alt="Logo" class="logo-img">
                <h1 class="school-name">City College of San Fernando – San Fernando Pampanga</h1>
            </div>

            <div class="overlay-text">
                <h1>VISION</h1>
                <p>By 2040, the City College of San Fernando Pampanga is a leading institution of higher education advancing the quality of life of Fernandinos.</p>
                <h1>MISSION</h1>
                <p>To provide innovative and industry-responsive education, sustained by community engagement with robust institutional structure.</p>
            </div>
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

                <div class="info-text">
                    Please login using your Google account to access your student portal.
                </div>
            </div>
        </div>
    </div>
  <script src="../public/js/login_index.js"></script>
</body>

</html>

