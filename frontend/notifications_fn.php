<?php
require_once __DIR__ . '/database.php';

function createNotification($userId, $senderId, $type, $message, $link = null, $referenceId = null, $referenceType = null) {
    if ($userId == $senderId) return;
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("INSERT IGNORE INTO notifications (user_id, sender_id, type, message, link, reference_id, reference_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $senderId, $type, $message, $link, $referenceId, $referenceType]);
}

function notifyFollowersNewPost($authorId, $postId, $authorName, $postTitle) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT follower_id FROM followers WHERE following_id = ?");
    $stmt->execute([$authorId]);
    $followers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $message   = htmlspecialchars($authorName, ENT_QUOTES, 'UTF-8') . ' posted: "' . htmlspecialchars(substr($postTitle, 0, 50), ENT_QUOTES, 'UTF-8') . '"';
    foreach ($followers as $f) {
        createNotification($f['follower_id'], $authorId, 'new_post', $message, "/post_details.php?id=$postId", $postId, 'post');
    }
}

function getNotifications($userId, $limit = 30) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT n.notification_id, n.type, n.message, n.link, n.reference_id, n.reference_type, n.is_read, n.created_at, u.name as sender_name, u.username as sender_username FROM notifications n JOIN users u ON n.sender_id = u.user_id WHERE n.user_id = ? ORDER BY n.created_at DESC LIMIT ?");
    $stmt->bindValue(1, (int)$userId, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$limit,  PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUnreadCount($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$userId]);
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function markAsRead($notificationId, $userId) {
    $pdo = getDBConnection();
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?")->execute([(int)$notificationId, (int)$userId]);
}

function markAllAsRead($userId) {
    $pdo = getDBConnection();
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([(int)$userId]);
}
?>
