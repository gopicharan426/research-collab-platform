<?php
/**
 * MongoDB Atlas Connection
 * Requires: mongodb/mongodb PHP library
 * Install via Composer: composer require mongodb/mongodb
 */

// Load .env file
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../../../.env');

function getMongoConnection() {
    static $client = null;
    if ($client) return $client;

    $uri = $_ENV['MONGODB_URI'] ?? '';
    if (empty($uri)) {
        die("MongoDB URI not set in .env");
    }

    // Requires MongoDB PHP library (composer require mongodb/mongodb)
    $client = new MongoDB\Client($uri);
    return $client;
}

function getMongoDB() {
    $dbName = $_ENV['MONGODB_DB'] ?? 'research_collab';
    return getMongoConnection()->selectDatabase($dbName);
}
?>
