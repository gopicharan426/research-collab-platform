<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/notifications.php';

function followUser($followerId, $followingId) {
    if ($followerId == $followingId) return "Cannot follow yourself.";
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("INSERT IGNORE INTO followers (follower_id, following_id) VALUES (?, ?)");
    if ($stmt->execute([$followerId, $followingId]) && $stmt->rowCount() > 0) {
        $s = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
        $s->execute([$followerId]);
        $sender = $s->fetch(PDO::FETCH_ASSOC);
        createNotification($followingId, $followerId, 'follow',
            htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . " started following you.",
            "/view_profile.php?id=$followerId"
        );
    }
    return "success";
}

function unfollowUser($followerId, $followingId) {
    $pdo = getDBConnection();
    $pdo->prepare("DELETE FROM followers WHERE follower_id = ? AND following_id = ?")->execute([$followerId, $followingId]);
    return "success";
}

function isFollowing($followerId, $followingId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM followers WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$followerId, $followingId]);
    return $stmt->fetch() ? true : false;
}

function getFollowers($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT u.user_id, u.name, u.username FROM followers f JOIN users u ON f.follower_id = u.user_id WHERE f.following_id = ? ORDER BY f.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFollowing($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT u.user_id, u.name, u.username FROM followers f JOIN users u ON f.following_id = u.user_id WHERE f.follower_id = ? ORDER BY f.created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFollowerCount($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM followers WHERE following_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getFollowingCount($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM followers WHERE follower_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>
