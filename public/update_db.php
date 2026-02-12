<?php
require_once '../app/config/database.php';

echo "<h2>Database Update</h2>";

try {
    $pdo = getDBConnection();
    
    // Add views column
    try {
        $pdo->exec("ALTER TABLE research_posts ADD COLUMN views INT DEFAULT 0");
        echo "✓ Added 'views' column to research_posts<br>";
    } catch(PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "✓ 'views' column already exists<br>";
        } else {
            throw $e;
        }
    }
    
    // Create post_likes table
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS post_likes (
                like_id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                user_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (post_id) REFERENCES research_posts(post_id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
                UNIQUE KEY unique_like (post_id, user_id)
            )
        ");
        echo "✓ Created 'post_likes' table<br>";
    } catch(PDOException $e) {
        echo "✓ 'post_likes' table already exists<br>";
    }
    
    echo "<br><strong>✓ Database updated successfully!</strong><br><br>";
    echo "<a href='index.php' style='padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px;'>Go to Application</a>";
    
} catch(PDOException $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>