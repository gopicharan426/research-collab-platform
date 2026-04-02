<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/notifications_fn.php';

function addComment($postId, $userId, $commentText) {
    $pdo = getDBConnection();
    if (empty($commentText)) return "Comment cannot be empty.";
    if (strlen($commentText) > 1000) return "Comment must be less than 1000 characters.";
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    if ($stmt->execute([$postId, $userId, $commentText])) {
        $p = $pdo->prepare("SELECT user_id, title FROM research_posts WHERE post_id = ?");
        $p->execute([$postId]); $post = $p->fetch(PDO::FETCH_ASSOC);
        $s = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
        $s->execute([$userId]); $sender = $s->fetch(PDO::FETCH_ASSOC);
        if ($post && $post['user_id'] != $userId)
            createNotification($post['user_id'], $userId, 'comment', htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' commented on your post "' . htmlspecialchars(substr($post['title'], 0, 40), ENT_QUOTES, 'UTF-8') . '"', "/post_details.php?id=$postId");
        return "success";
    }
    return "Failed to add comment.";
}

function getCommentsByPost($postId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT c.comment_text, c.created_at, u.name as author_name FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.post_id = ? ORDER BY c.created_at ASC");
    $stmt->execute([$postId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCommentCount($postId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE post_id = ?");
    $stmt->execute([$postId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}
?>
