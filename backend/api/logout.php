<?php
session_start();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    require_once '../config/database.php';
    
    // Delete token from database
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("
        DELETE FROM remember_tokens WHERE token = :token
    ");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    
    // Clear cookie
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Destroy session
session_unset();
session_destroy();

// Redirect to home page
header("Location: ../../index.php");
exit;