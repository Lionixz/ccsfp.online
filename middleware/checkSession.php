<?php
$client = require __DIR__ . '/../config/config.php';
$conn = require __DIR__ . '/../config/db.php';

session_start();

// Check if user session exists
if (!isset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_picture'], $_SESSION['session_token'])) {
    header("Location: ../public/index.php");
    exit;
}

$google_id = $_SESSION['user_id'];
$session_token = $_SESSION['session_token'];

// Fetch user info from database
$stmt = $conn->prepare("SELECT id, session_token, role FROM users WHERE google_id = ?");
$stmt->bind_param('s', $google_id);
$stmt->execute();
$stmt->bind_result($db_user_id, $db_session_token, $db_role);
$stmt->fetch();
$stmt->close();

// Force logout if session token mismatch
if ($session_token !== $db_session_token) {
    header("Location: ../middleware/forced_logout.php");
    exit;
}

// Update last_seen timestamp
date_default_timezone_set('Asia/Manila');
$stmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE google_id = ?");
$stmt->bind_param('s', $google_id);
$stmt->execute();
$stmt->close();

// Store DB user ID in session if missing
$_SESSION['db_user_id'] = $_SESSION['db_user_id'] ?? $db_user_id;
$_SESSION['db_user_role'] = $_SESSION['db_user_role'] ?? $db_role;
