<?php
require __DIR__ . '/../config/db.php';
$stmt = $mysqli->prepare("
    SELECT COUNT(*) 
    FROM users 
    WHERE last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
");
$stmt->execute();
$stmt->bind_result($onlineCount);
$stmt->fetch();
$stmt->close();
echo $onlineCount;
