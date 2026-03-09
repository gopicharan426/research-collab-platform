<?php
require_once '../app/config/database.php';

// Create new research post
function createPost($userId, $title, $description) {
    $pdo = getDBConnection();
    
    if (empty($title) || empty($description)) {
        return "Title and description are required.";
    }
    
    if (strlen($title) > 200) {
        return "Title must be less than 200 characters.";
    }
    
    $stmt = $pdo->prepare("INSERT INTO research_posts (user_id, title, description) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$userId, $title, $description])) {
        return "success";
    }
    return "Failed to create post.";
}

// Get all research posts with author names
function getAllPosts($search = '') {
    $pdo = getDBConnection();
    
    if (!empty($search)) {
        $stmt = $pdo->prepare("
            SELECT p.post_id, p.title, p.description, p.created_at, u.name as author_name 
            FROM research_posts p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE p.title LIKE ? OR p.description LIKE ?
            ORDER BY p.created_at DESC
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare("
            SELECT p.post_id, p.title, p.description, p.created_at, u.name as author_name 
            FROM research_posts p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get single post with details
function getPostById($postId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT p.post_id, p.title, p.description, p.created_at, p.user_id, u.name as author_name 
        FROM research_posts p 
        JOIN users u ON p.user_id = u.user_id 
        WHERE p.post_id = ?
    ");
    $stmt->execute([$postId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get posts by user
function getPostsByUser($userId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT post_id, title, description, created_at, views
        FROM research_posts 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Delete post
function deletePost($postId, $userId, $isAdmin = false) {
    $pdo = getDBConnection();
    
    if ($isAdmin) {
        $stmt = $pdo->prepare("DELETE FROM research_posts WHERE post_id = ?");
        if ($stmt->execute([$postId])) {
            return "success";
        }
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM research_posts WHERE post_id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post && $post['user_id'] == $userId) {
            $stmt = $pdo->prepare("DELETE FROM research_posts WHERE post_id = ?");
            if ($stmt->execute([$postId])) {
                return "success";
            }
        }
    }
    return "Failed to delete post.";
}
?>