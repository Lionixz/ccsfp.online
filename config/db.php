<?php
$host = "localhost";
$dbname = "ccsfp";
$username = "root";
$password = "";

// $host = "localhost"; // usually still localhost in Hostinger
// $dbname = "u992785675_ccsfp_db";
// $username = "u992785675_ccsfp_user";
// $password = "*Q8ut2@W>jp";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
return $conn;
