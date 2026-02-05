<?php
session_start();
$message = $_SESSION['block_message'] ?? "Your account has restricted access.";
$blocked_status = $_SESSION['blocked_status'] ?? '';

session_unset();
session_destroy(); // destroy AFTER reading the values
?>

<meta http-equiv="refresh" content="10;url=../public/index.php">

<div class="logout-warning-container">
    <h2>⚠️ Account Blocked</h2>
    <p><?= htmlspecialchars($message) ?></p>
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

    /* Container styling */
    .logout-warning-container {
        max-width: 480px;
        width: 90%;
        padding: 30px;
        background: linear-gradient(145deg, #1e1e2f, #2a2a40);
        border: 1px solid #3e3e5e;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 0 15px rgba(93, 121, 255, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .logout-warning-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(93, 121, 255, 0.5);
    }

    /* Heading */
    .logout-warning-container h2 {
        margin-bottom: 15px;
        font-size: 24px;
        font-weight: 600;
        color: #ff9c9c;
    }

    /* Message text */
    .logout-warning-container p {
        font-size: 16px;
        margin-bottom: 20px;
        color: #d2d2e0;
        line-height: 1.5;
        word-wrap: break-word;
    }

    /* Redirect text */
    .logout-warning-container .redirect {
        font-size: 14px;
        margin-top: 10px;
        color: #9cc6ff;
    }

    /* Login button */
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
    }

    /* Make sure long messages break properly */
    .logout-warning-container p {
        overflow-wrap: break-word;
        word-break: break-word;
    }
</style>