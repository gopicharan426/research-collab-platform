<?php
require_once '../app/config/database.php';

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = getDBConnection();
    echo "✓ Database connected successfully<br><br>";
    
    // Check tables
    echo "<h3>Checking Tables:</h3>";
    
    $tables = ['users', 'research_posts', 'comments'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists<br>";
        } else {
            echo "✗ Table '$table' NOT FOUND<br>";
        }
    }
    
    echo "<br><h3>User Count:</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total users: " . $result['count'] . "<br>";
    
    echo "<br><a href='index.php'>Go to Application</a>";
    
} catch(Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>