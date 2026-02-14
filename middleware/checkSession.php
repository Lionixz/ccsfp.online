<?php
$client = require __DIR__ . '/../config/config.php';
$conn = require __DIR__ . '/../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // start session only if none active
}

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

// Validate session token
if ($session_token !== $db_session_token) {
    session_destroy();
    header("Location: ../public/index.php");
    exit;
}

