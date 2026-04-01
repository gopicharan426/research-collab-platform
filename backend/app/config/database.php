<?php
// Load .env file if not on Render (local development)
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

// Auto-load MongoDB library installed via Composer
$autoload = __DIR__ . '/../../../vendor/autoload.php';
if (file_exists($autoload)) require_once $autoload;

/**
 * Get MongoDB database instance (singleton)
 */
function getDB() {
    static $db = null;
    if ($db) return $db;

    $uri    = $_ENV['MONGODB_URI'] ?? getenv('MONGODB_URI');
    $dbName = $_ENV['MONGODB_DB']  ?? getenv('MONGODB_DB') ?? 'research_collab';

    if (empty($uri)) die(json_encode(['error' => 'MONGODB_URI not set']));

    $client = new MongoDB\Client($uri);
    $db     = $client->selectDatabase($dbName);
    return $db;
}

/**
 * Get a MongoDB collection
 */
function getCollection($name) {
    return getDB()->selectCollection($name);
}

/**
 * Generate next auto-increment ID for a collection
 * MongoDB doesn't have auto-increment, so we simulate it
 */
function getNextId($collection) {
    $db = getDB();
    $result = $db->selectCollection('counters')->findOneAndUpdate(
        ['_id' => $collection],
        ['$inc' => ['seq' => 1]],
        ['upsert' => true, 'returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]
    );
    return (int)$result['seq'];
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
