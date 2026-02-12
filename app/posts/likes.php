<?php
require_once '../app/config/database.php';

// Toggle like on post
function toggleLike($postId, $userId) {
    $pdo = getDBConnection();
    
    // Check if already liked
    $stmt = $pdo->prepare("SELECT like_id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    
    if ($stmt->fetch()) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        return "unliked";
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$postId, $userId]);
        return "liked";
    }
}

// Get like count for post
function getLikeCount($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?");
    $stmt->execute([$postId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Check if user liked post
function hasUserLiked($postId, $userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT like_id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    return $stmt->fetch() ? true : false;
}

// Increment view count
function incrementViews($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE research_posts SET views = views + 1 WHERE post_id = ?");
    $stmt->execute([$postId]);
}

// Get view count
function getViewCount($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT views FROM research_posts WHERE post_id = ?");
    $stmt->execute([$postId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['views'] ?? 0;
}
?>