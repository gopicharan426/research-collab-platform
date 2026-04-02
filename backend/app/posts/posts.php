<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../social/notifications.php';

function createPost($userId, $title, $description) {
    $pdo = getDBConnection();
    if (empty($title) || empty($description)) return "Title and description are required.";
    if (strlen($title) > 200) return "Title must be less than 200 characters.";
    $stmt = $pdo->prepare("INSERT INTO research_posts (user_id, title, description) VALUES (?, ?, ?)");
    if ($stmt->execute([$userId, $title, $description])) {
        $postId = $pdo->lastInsertId();
        $nameStmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
        $nameStmt->execute([$userId]);
        $author = $nameStmt->fetch(PDO::FETCH_ASSOC);
        notifyFollowersNewPost($userId, $postId, $author['name'], $title);
        return "success";
    }
    return "Failed to create post.";
}

function getAllPosts($search = '', $currentUserId = 0) {
    $pdo = getDBConnection();
    $followPriority = $currentUserId ? "CASE WHEN f.follower_id = $currentUserId THEN 0 ELSE 1 END" : "1";
    if (!empty($search)) {
        $searchTerm = '%' . $search . '%';
        $stmt = $pdo->prepare("
            SELECT p.post_id, p.title, p.description, p.created_at, p.views,
                   u.name as author_name, u.user_id as author_id, u.username as author_username,
                   $followPriority as feed_priority
            FROM research_posts p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN followers f ON f.following_id = p.user_id AND f.follower_id = ?
            WHERE p.title LIKE ? OR p.description LIKE ?
            ORDER BY feed_priority ASC, p.created_at DESC
        ");
        $stmt->execute([$currentUserId, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare("
            SELECT p.post_id, p.title, p.description, p.created_at, p.views,
                   u.name as author_name, u.user_id as author_id, u.username as author_username,
                   $followPriority as feed_priority
            FROM research_posts p
            JOIN users u ON p.user_id = u.user_id
            LEFT JOIN followers f ON f.following_id = p.user_id AND f.follower_id = ?
            ORDER BY feed_priority ASC, p.created_at DESC
        ");
        $stmt->execute([$currentUserId]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPostById($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT p.post_id, p.title, p.description, p.created_at, p.user_id, u.name as author_name FROM research_posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getPostsByUser($userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT post_id, title, description, created_at, views FROM research_posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deletePost($postId, $userId, $isAdmin = false) {
    $pdo = getDBConnection();
    if ($isAdmin) {
        $stmt = $pdo->prepare("DELETE FROM research_posts WHERE post_id = ?");
        if ($stmt->execute([$postId])) return "success";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM research_posts WHERE post_id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($post && $post['user_id'] == $userId) {
            $stmt = $pdo->prepare("DELETE FROM research_posts WHERE post_id = ?");
            if ($stmt->execute([$postId])) return "success";
        }
    }
    return "Failed to delete post.";
}
?>
