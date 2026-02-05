<?php
$host = "localhost";
$dbname = "ccsfp";
$username = "root";
$password = "";

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
return $mysqli;
