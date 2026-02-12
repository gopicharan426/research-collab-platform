<?php
// Database configuration for hosting
// Update these values with your hosting provider's database details

// For local development (XAMPP)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'research_collab');

// For online hosting - uncomment and update these lines:
/*
define('DB_HOST', 'your_host_here');        // e.g., 'sql123.000webhost.com'
define('DB_USER', 'your_username_here');    // e.g., 'id12345_username'
define('DB_PASS', 'your_password_here');    // Your database password
define('DB_NAME', 'your_database_here');    // e.g., 'id12345_research_collab'
*/

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>