<?php
session_start();
require_once '../config/database.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $terms = isset($_POST['terms']) ? (bool)$_POST['terms'] : false;
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '';
    
    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        header("Location: ../../register.php?error=empty" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    if ($password !== $confirmPassword) {
        header("Location: ../../register.php?error=password" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    if (!$terms) {
        header("Location: ../../register.php?error=terms" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM users WHERE email = :email
    ");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        header("Location: ../../register.php?error=email" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password)
        VALUES (:name, :email, :password)
    ");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);
    
    if ($stmt->execute()) {
        // Get new user ID
        $userId = $conn->lastInsertId();
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['is_admin'] = false;
        
        // Redirect based on redirect parameter
        if (!empty($redirect)) {
            header("Location: ../../$redirect.php");
        } else {
            header("Location: ../../index.php");
        }
        exit;
    } else {
        header("Location: ../../register.php?error=unknown" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
}

// If not a POST request, redirect to registration page
header("Location: ../../register.php");
exit;