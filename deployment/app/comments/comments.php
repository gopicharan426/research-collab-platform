<?php
require_once '../app/config/database.php';

// Add comment to post
function addComment($postId, $userId, $commentText) {
    $pdo = getDBConnection();
    
    if (empty($commentText)) {
        return "Comment cannot be empty.";
    }
    
    if (strlen($commentText) > 1000) {
        return "Comment must be less than 1000 characters.";
    }
    
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$postId, $userId, $commentText])) {
        return "success";
    }
    return "Failed to add comment.";
}

// Get comments for a post
function getCommentsByPost($postId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT c.comment_text, c.created_at, u.name as author_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$postId]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get comment count for a post
function getCommentCount($postId) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE post_id = ?");
    $stmt->execute([$postId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'];
}
?>