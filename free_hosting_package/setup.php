<?php
// Database setup script
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS research_collab");
    echo "Database 'research_collab' created successfully.<br>";
    
    // Use the database
    $pdo->exec("USE research_collab");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Users table created successfully.<br>";
    
    // Create research_posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS research_posts (
            post_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(200) NOT NULL,
            description TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )
    ");
    echo "Research posts table created successfully.<br>";
    
    // Create comments table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            comment_id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            user_id INT NOT NULL,
            comment_text TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES research_posts(post_id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        )
    ");
    echo "Comments table created successfully.<br>";
    
    // Insert test user
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', $hashedPassword]);
    echo "Test user created successfully.<br>";
    
    // Insert sample post
    $stmt = $pdo->prepare("INSERT IGNORE INTO research_posts (user_id, title, description) VALUES (?, ?, ?)");
    $stmt->execute([1, 'AI in Healthcare Research', 'Exploring machine learning applications in medical diagnosis and treatment optimization.']);
    echo "Sample post created successfully.<br>";
    
    // Insert sample comment
    $stmt = $pdo->prepare("INSERT IGNORE INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([1, 1, 'This is a fascinating area of research with great potential for improving patient outcomes.']);
    echo "Sample comment created successfully.<br>";
    
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "<a href='index.php'>Go to Application</a>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>