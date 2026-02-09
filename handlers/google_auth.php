<?php

use Google\Service\Oauth2;

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

function logoutAndRedirect($url = "index.php")
{
    session_unset();
    session_destroy();
    header("Location: $url");
    exit;
}

/**
 * =========================================
 * REUSE SESSION TOKEN IF EXISTS
 * =========================================
 */
if (!empty($_SESSION['access_token'])) {
    $client->setAccessToken($_SESSION['access_token']);

    if ($client->isAccessTokenExpired()) {
        try {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $_SESSION['access_token'] = $client->getAccessToken();
        } catch (Exception $e) {
            error_log($e->getMessage());
            logoutAndRedirect();
        }
    }
}

/**
 * =========================================
 * GOOGLE CALLBACK HANDLER
 * =========================================
 */
if (!empty($_GET['code']) && empty($_SESSION['access_token'])) {

    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (!empty($token['error'])) {
            throw new Exception($token['error']);
        }

        $client->setAccessToken($token['access_token']);
        $_SESSION['access_token'] = $token['access_token'];

        $oauth = new Oauth2($client);
        $user  = $oauth->userinfo->get();

        $_SESSION['user_id']      = $user->id;
        $_SESSION['user_email']   = $user->email;
        $_SESSION['user_name']    = $user->name;
        $_SESSION['user_picture'] = $user->picture;

        $now = date('Y-m-d H:i:s');

        // Check existing user
        $stmt = $conn->prepare("SELECT id, role FROM users WHERE google_id = ?");
        $stmt->bind_param("s", $user->id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $role = 'user';
            $stmt = $conn->prepare(
                "INSERT INTO users (google_id, name, email, picture, role, last_seen)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssssss",
                $user->id,
                $user->name,
                $user->email,
                $user->picture,
                $role,
                $now
            );
            $stmt->execute();
            $user_id = $conn->insert_id;
        } else {
            $row = $res->fetch_assoc();
            $user_id = $row['id'];
            $role = $row['role'];

            $stmt = $conn->prepare(
                "UPDATE users SET last_seen = ? WHERE google_id = ?"
            );
            $stmt->bind_param("ss", $now, $user->id);
            $stmt->execute();
        }

        // Generate session token
        $session_token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare(
            "UPDATE users SET session_token = ? WHERE google_id = ?"
        );
        $stmt->bind_param("ss", $session_token, $user->id);
        $stmt->execute();

        $_SESSION['session_token'] = $session_token;
        $_SESSION['role'] = $role;

        header("Location: " . ($role === 'admin' ? "../admin/index.php" : "../users/index.php"));
        exit;
    } catch (Exception $e) {
        error_log("Google Auth Error: " . $e->getMessage());
        logoutAndRedirect("../error.php");
    }
}
