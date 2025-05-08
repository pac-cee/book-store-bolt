<?php
session_start();
require_once '../config/database.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? (bool)$_POST['remember'] : false;
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : '';
    
    // Validate form data
    if (empty($email) || empty($password)) {
        header("Location: ../../login.php?error=empty" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    // Check if user exists
    $stmt = $conn->prepare("
        SELECT id, name, email, password, is_admin
        FROM users
        WHERE email = :email
    ");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        header("Location: ../../login.php?error=invalid" . ($redirect ? "&redirect=$redirect" : ''));
        exit;
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['is_admin'] = (bool)$user['is_admin'];
    
    // Set remember me cookie if requested
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Save token in database
        $stmt = $conn->prepare("
            INSERT INTO remember_tokens (user_id, token, expires_at)
            VALUES (:user_id, :token, :expires_at)
        ");
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expires));
        $stmt->execute();
        
        // Set cookie
        setcookie('remember_token', $token, $expires, '/', '', false, true);
    }
    
    // Redirect based on user type and redirect parameter
    if (!empty($redirect)) {
        header("Location: ../../$redirect.php");
    } elseif ($user['is_admin']) {
        header("Location: ../../admin/index.php");
    } else {
        header("Location: ../../index.php");
    }
    exit;
}

// If not a POST request, redirect to login page
header("Location: ../../login.php");
exit;