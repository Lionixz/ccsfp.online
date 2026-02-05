<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    $google_id = $mysqli->real_escape_string($_SESSION['user_id']);

    // Update last_seen to current timestamp and invalidate session token
    $stmt = $mysqli->prepare("UPDATE users SET last_seen = NOW(), session_token = NULL WHERE google_id = ?");
    $stmt->bind_param('s', $google_id);
    $stmt->execute();
    $stmt->close();
}

// Destroy session
session_unset();
session_destroy();


// Redirect to login page
header('Location: ../public/index.php');
exit;
