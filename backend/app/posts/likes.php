<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../social/notifications.php';

function toggleLike($postId, $userId) {
    $col      = getCollection('post_likes');
    $posts    = getCollection('research_posts');
    $users    = getCollection('users');
    $existing = $col->findOne(['post_id' => (int)$postId, 'user_id' => (int)$userId]);

    if ($existing) {
        $col->deleteOne(['post_id' => (int)$postId, 'user_id' => (int)$userId]);
        return "unliked";
    }

    $col->insertOne(['post_id' => (int)$postId, 'user_id' => (int)$userId, 'created_at' => date('Y-m-d H:i:s')]);

    $post   = $posts->findOne(['post_id' => (int)$postId]);
    $sender = $users->findOne(['user_id' => (int)$userId]);

    if ($post && (int)$post['user_id'] !== (int)$userId) {
        createNotification(
            (int)$post['user_id'], (int)$userId, 'like',
            htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' liked your post "' . htmlspecialchars(substr($post['title'], 0, 40), ENT_QUOTES, 'UTF-8') . '"',
            "/post_details.php?id=$postId"
        );
    }
    return "liked";
}

function getLikeCount($postId) {
    return (int)getCollection('post_likes')->countDocuments(['post_id' => (int)$postId]);
}

function hasUserLiked($postId, $userId) {
    return (bool)getCollection('post_likes')->findOne(['post_id' => (int)$postId, 'user_id' => (int)$userId]);
}

function incrementViews($postId) {
    getCollection('research_posts')->updateOne(['post_id' => (int)$postId], ['$inc' => ['views' => 1]]);
}

function getViewCount($postId) {
    $post = getCollection('research_posts')->findOne(['post_id' => (int)$postId]);
    return (int)($post['views'] ?? 0);
}
?>
