<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/recaptcha_config.php';

function verifyRecaptcha($response) { return true; }

function registerUser($name, $username, $email, $password, $role, $department, $class = null, $designation = null, $recaptchaResponse = null) {
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

    $col = getCollection('users');

    if ($col->findOne(['email' => $email])) return "Email already registered.";
    if ($col->findOne(['username' => $username])) return "Username already taken.";

    $userId = getNextId('users');
    $col->insertOne([
        'user_id'     => $userId,
        'name'        => htmlspecialchars(trim($name), ENT_QUOTES, 'UTF-8'),
        'username'    => $username,
        'email'       => htmlspecialchars(trim($email), ENT_QUOTES, 'UTF-8'),
        'password'    => password_hash($password, PASSWORD_DEFAULT),
        'role'        => $role,
        'department'  => htmlspecialchars(trim($department), ENT_QUOTES, 'UTF-8'),
        'class'       => $class ? htmlspecialchars(trim($class), ENT_QUOTES, 'UTF-8') : null,
        'designation' => $designation ? htmlspecialchars(trim($designation), ENT_QUOTES, 'UTF-8') : null,
        'is_admin'    => 0,
        'bio'         => null, 'profile_picture' => null, 'phone' => null,
        'location'    => null, 'website' => null, 'linkedin' => null,
        'research_interests' => null,
        'created_at'  => date('Y-m-d H:i:s')
    ]);
    return "success";
}

function loginUser($email, $password) {
    if (empty($email) || empty($password)) return "Email and password are required.";
    $col  = getCollection('users');
    $user = $col->findOne(['email' => $email]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']       = (int)$user['user_id'];
        $_SESSION['user_name']     = $user['name'];
        $_SESSION['user_username'] = $user['username'] ?? '';
        $_SESSION['user_email']    = $email;
        $_SESSION['is_admin']      = (int)($user['is_admin'] ?? 0);
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
