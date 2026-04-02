<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/notifications_fn.php';

function toggleLike($postId, $userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT like_id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    if ($stmt->fetch()) {
        $pdo->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?")->execute([$postId, $userId]);
        return "unliked";
    }
    $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)")->execute([$postId, $userId]);
    $p = $pdo->prepare("SELECT user_id, title FROM research_posts WHERE post_id = ?");
    $p->execute([$postId]); $post = $p->fetch(PDO::FETCH_ASSOC);
    $s = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
    $s->execute([$userId]); $sender = $s->fetch(PDO::FETCH_ASSOC);
    if ($post && $post['user_id'] != $userId)
        createNotification($post['user_id'], $userId, 'like', htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' liked your post "' . htmlspecialchars(substr($post['title'], 0, 40), ENT_QUOTES, 'UTF-8') . '"', "/post_details.php?id=$postId");
    return "liked";
}

function getLikeCount($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM post_likes WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function hasUserLiked($postId, $userId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT like_id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);
    return $stmt->fetch() ? true : false;
}

function incrementViews($postId) {
    getDBConnection()->prepare("UPDATE research_posts SET views = views + 1 WHERE post_id = ?")->execute([$postId]);
}

function getViewCount($postId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT views FROM research_posts WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['views'] ?? 0;
}
?>
