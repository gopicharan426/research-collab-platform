<?php
function getDBConnection() {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO("mysql:host=sql110.infinityfree.com;dbname=if0_41083287_research_collab_export;charset=utf8", "if0_41083287", "kavi1618");
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
