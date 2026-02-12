<?php
require_once '../app/config/database.php';

try {
    $pdo = getDBConnection();
    $sql = "ALTER TABLE users 
            ADD COLUMN reset_token VARCHAR(64) NULL, 
            ADD COLUMN reset_token_expiry DATETIME NULL";
    
    $pdo->exec($sql);
    echo "✓ Successfully added reset_token and reset_token_expiry columns to users table!";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Columns already exist - no action needed!";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
