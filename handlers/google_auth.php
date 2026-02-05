<?php

use Google\Service\Oauth2;

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Set PHP timezone globally
date_default_timezone_set('Asia/Manila');

function safeRedirect($url)
{
    header("Location: $url");
    exit;
}

// Already logged in?
if (isset($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);

    if ($client->isAccessTokenExpired()) {
        $refreshToken = $client->getRefreshToken();
        if ($refreshToken) {
            try {
                $client->fetchAccessTokenWithRefreshToken($refreshToken);
                $_SESSION['access_token'] = $client->getAccessToken();
            } catch (Exception $e) {
                error_log("Google Auth Refresh Error: " . $e->getMessage());
                session_unset();
                session_destroy();
                safeRedirect("index.php");
            }
        } else {
            session_unset();
            session_destroy();
            safeRedirect("index.php");
        }
    }
}

// Handle authorization flow
if (isset($_GET['code']) && !isset($_SESSION['access_token'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (!isset($token['error'])) {
            $client->setAccessToken($token['access_token']);
            $_SESSION['access_token'] = $token['access_token'];

            $oauth = new Oauth2($client);
            $user = $oauth->userinfo->get();

            // Save user info in session
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_picture'] = $user->picture;

            $google_id = $mysqli->real_escape_string($user->id);
            $name      = $mysqli->real_escape_string($user->name);
            $email     = $mysqli->real_escape_string($user->email);
            $picture   = $mysqli->real_escape_string($user->picture);

            // Get current Manila time
            $now = date('Y-m-d H:i:s');

            // Check if user exists
            $res = $mysqli->prepare("SELECT id, role FROM users WHERE google_id = ?");
            $res->bind_param("s", $google_id);
            $res->execute();
            $result = $res->get_result();

            if ($result->num_rows === 0) {
                // Insert new user
                $insertUser = $mysqli->prepare("
                    INSERT INTO users (google_id, name, email, picture, role, last_seen)
                    VALUES (?, ?, ?, ?, 'user', ?)
                ");
                $insertUser->bind_param("sssss", $google_id, $name, $email, $picture, $now);
                $insertUser->execute();
                $user_id = $mysqli->insert_id;
                $role = 'user';
                $insertUser->close();
            } else {
                $row = $result->fetch_assoc();
                $role = $row['role'];
                $user_id = $row['id'];

                // Update last_seen
                $updateLastSeen = $mysqli->prepare("UPDATE users SET last_seen = ? WHERE google_id = ?");
                $updateLastSeen->bind_param("ss", $now, $google_id);
                $updateLastSeen->execute();
                $updateLastSeen->close();
            }
            $res->close();

            // Generate session token
            $session_token = bin2hex(random_bytes(32));
            $updateToken = $mysqli->prepare("UPDATE users SET session_token = ? WHERE google_id = ?");
            $updateToken->bind_param("ss", $session_token, $google_id);
            $updateToken->execute();
            $updateToken->close();

            $_SESSION['session_token'] = $session_token;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'admin') {
                safeRedirect("../admin/index.php");
            } else {
                safeRedirect("../users/index.php");
            }
        } else {
            error_log("Google Token Error: " . $token['error']);
            safeRedirect("../error.php");
        }
    } catch (Exception $e) {
        error_log("Google Auth Error: " . $e->getMessage());
        safeRedirect("../error.php");
    }
}
