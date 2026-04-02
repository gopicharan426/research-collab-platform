<?php
// Load .env file for local development
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key); $value = trim($value);
        if (!isset($_ENV[$key])) $_ENV[$key] = $value;
        if (!getenv($key)) putenv("$key=$value");
    }
}
loadEnv(__DIR__ . '/../../../.env');

// MySQL connection via PDO
function getDBConnection() {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'research_collab';
        $pdo  = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("MySQL connection failed: " . $e->getMessage());
    }
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
