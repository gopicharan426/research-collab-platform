<?php
require_once __DIR__ . '/../config/database.php';

function createNotification($userId, $senderId, $type, $message, $link = null, $referenceId = null, $referenceType = null) {
    if ($userId == $senderId) return;
    $col = getCollection('notifications');
    // Prevent duplicates
    $existing = $col->findOne(['user_id' => (int)$userId, 'sender_id' => (int)$senderId, 'type' => $type, 'reference_id' => $referenceId]);
    if ($existing) return;

    $notifId = getNextId('notifications');
    $col->insertOne([
        'notification_id' => $notifId,
        'user_id'         => (int)$userId,
        'sender_id'       => (int)$senderId,
        'type'            => $type,
        'message'         => $message,
        'link'            => $link,
        'reference_id'    => $referenceId,
        'reference_type'  => $referenceType,
        'is_read'         => 0,
        'created_at'      => date('Y-m-d H:i:s')
    ]);
}

function notifyFollowersNewPost($authorId, $postId, $authorName, $postTitle) {
    $followers = getCollection('followers')->find(['following_id' => (int)$authorId])->toArray();
    $message   = htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') . ' posted: "' . htmlspecialchars(substr($postTitle, 0, 50), ENT_QUOTES, 'UTF-8') . '"';
    $link      = "/post_details.php?id=$postId";
    foreach ($followers as $f) {
        createNotification((int)$f['follower_id'], (int)$authorId, 'new_post', $message, $link, $postId, 'post');
    }
}

function getNotifications($userId, $limit = 30) {
    $col    = getCollection('notifications');
    $users  = getCollection('users');
    $notifs = $col->find(['user_id' => (int)$userId], ['sort' => ['created_at' => -1], 'limit' => $limit])->toArray();

    return array_map(function($n) use ($users) {
        $sender = $users->findOne(['user_id' => (int)$n['sender_id']]);
        return [
            'notification_id'  => (int)$n['notification_id'],
            'type'             => $n['type'],
            'message'          => $n['message'],
            'link'             => $n['link'] ?? null,
            'reference_id'     => $n['reference_id'] ?? null,
            'reference_type'   => $n['reference_type'] ?? null,
            'is_read'          => (int)$n['is_read'],
            'created_at'       => $n['created_at'],
            'sender_name'      => $sender['name'] ?? 'Unknown',
            'sender_username'  => $sender['username'] ?? ''
        ];
    }, $notifs);
}

function getUnreadCount($userId) {
    return (int)getCollection('notifications')->countDocuments(['user_id' => (int)$userId, 'is_read' => 0]);
}

function markAsRead($notificationId, $userId) {
    getCollection('notifications')->updateOne(
        ['notification_id' => (int)$notificationId, 'user_id' => (int)$userId],
        ['$set' => ['is_read' => 1]]
    );
}

function markAllAsRead($userId) {
    getCollection('notifications')->updateMany(['user_id' => (int)$userId], ['$set' => ['is_read' => 1]]);
}
?>
