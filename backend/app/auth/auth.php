<?php
require_once __DIR__ . '/../config/database.php';

// Register new user
function registerUser($name, $email, $password) {
    $pdo = getDBConnection();
    
    // Validate input
    if (empty($name) || empty($email) || empty($password)) {
        return "All fields are required.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }
    
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters.";
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return "Email already registered.";
    }
    
    // Hash password and insert user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$name, $email, $hashedPassword])) {
        return "success";
    }
    return "Registration failed.";
}

// Login user
function loginUser($email, $password) {
    $pdo = getDBConnection();
    
    if (empty($email) || empty($password)) {
        return "Email and password are required.";
    }
    
    $stmt = $pdo->prepare("SELECT user_id, name, password, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $email;
        $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
        return "success";
    }
    return "Invalid email or password.";
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Logout user
function logoutUser() {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
?>