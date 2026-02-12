<?php
include('../middleware/checkSession.php');
include_once('../middleware/cache.php');
require_once '../config/db.php';

$applicant_id = $_GET['id'] ?? null;
if (!$applicant_id) {
    die("No applicant ID provided.");
}

$stmt = $conn->prepare("SELECT photo FROM applicants WHERE id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();  
$applicant = $result->fetch_assoc();
$stmt->close(); 

if (!$applicant) {
    die("Applicant not found.");
}

if (!empty($applicant['photo'])) {
    $photoPath = __DIR__ . '/../public/images/uploads/' . basename($applicant['photo']);
    
    if (file_exists($photoPath)) {
        unlink($photoPath); // delete the file
    }
}

$stmt = $conn->prepare("DELETE FROM applicants WHERE id = ?");
$stmt->bind_param("i", $applicant_id); 

if ($stmt->execute()) {
    $stmt->close(); 
    
    header("Location: index.php?msg=deleted");
    exit;
} else {
    die("Failed to delete applicant: " . $conn->error);
}