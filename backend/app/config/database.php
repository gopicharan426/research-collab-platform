<?php
function getDBConnection() {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=research_collab;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
