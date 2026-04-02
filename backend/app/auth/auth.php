<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/recaptcha_config.php';

function verifyRecaptcha($response) { return true; }

function registerUser($name, $username, $email, $password, $role, $department, $class = null, $designation = null, $recaptchaResponse = null) {
    $pdo = getDBConnection();
    if (!verifyRecaptcha($recaptchaResponse)) return "CAPTCHA verification failed.";
    if (empty($name) || empty($username) || empty($email) || empty($password) || empty($role) || empty($department))
        return "All required fields must be filled.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Invalid email format.";
    if (strlen($password) < 6) return "Password must be at least 6 characters.";
    if (!in_array($role, ['student', 'professor'])) return "Invalid role selected.";
    if ($role === 'student' && empty($class)) return "Class is required for students.";
    if ($role === 'professor' && empty($designation)) return "Designation is required for professors.";
    $username = trim($username);
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
        return "Username must be 3-30 characters (letters, numbers, underscores only).";
    $name        = htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8');
    $email       = htmlspecialchars(trim($email), ENT_QUOTES, 'UTF-8');
    $department  = htmlspecialchars(trim($department), ENT_QUOTES, 'UTF-8');
    $class       = $class ? htmlspecialchars(trim($class), ENT_QUOTES, 'UTF-8') : null;
    $designation = $designation ? htmlspecialchars(trim($designation), ENT_QUOTES, 'UTF-8') : null;
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) return "Email already registered.";
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) return "Username already taken. Please choose another.";
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, role, department, class, designation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $username, $email, $hashedPassword, $role, $department, $class, $designation]))
        return "success";
    return "Registration failed. Please try again.";
}

function loginUser($email, $password) {
    $pdo = getDBConnection();
    if (empty($email) || empty($password)) return "Email and password are required.";
    $stmt = $pdo->prepare("SELECT user_id, name, username, password, is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']       = $user['user_id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email']    = $email;
        $_SESSION['is_admin']      = $user['is_admin'] ?? 0;
        return "success";
    }
    return "Invalid email or password.";
}

function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin()    { return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; }

function logoutUser() {
    session_destroy();
    header("Location: login.php");
    exit();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}
?>
