<!-- C:\xampp\htdocs\x\middleware\forced_logout.php -->
<?php
session_start();
session_unset();
session_destroy();
?>

<meta http-equiv="refresh" content="10;url=../public/index.php">


<div class="logout-warning-container">
    <h2>⚠️ Forced Logout</h2>
    <p>Your account was logged in from another device.<br>
        For your security, you have been logged out.</p>
    <a href="../public/index.php">🔄 Go to Login</a>
    <p class="redirect">Redirecting in 10 seconds...</p>
</div>




<style>
    /* Reset body and center content */
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        height: 100vh;
        background-color: #121226;
        font-family: 'Poppins', sans-serif;
    }

    .logout-warning-container {
        max-width: 480px;
        padding: 30px;
        background: linear-gradient(145deg, #1e1e2f, #2a2a40);
        border: 1px solid #3e3e5e;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 0 15px rgba(93, 121, 255, 0.3);
    }

    .logout-warning-container h2 {
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: 600;
        color: #ff9c9c;
    }

    .logout-warning-container p {
        font-size: 16px;
        margin-bottom: 20px;
        color: #d2d2e0;
    }

    .logout-warning-container .redirect {
        font-size: 14px;
        margin-top: 10px;
        color: #9cc6ff;
    }

    .logout-warning-container a {
        display: inline-block;
        padding: 12px 24px;
        background: linear-gradient(90deg, #5e63ff, #8298ff);
        color: #ffffff;
        font-weight: 500;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 0 10px rgba(130, 152, 255, 0.4);
    }

    .logout-warning-container a:hover {
        background: linear-gradient(90deg, #4c50e0, #6a80ff);
        box-shadow: 0 0 12px rgba(130, 152, 255, 0.6);
        transform: translateY(-2px);
    }
</style>