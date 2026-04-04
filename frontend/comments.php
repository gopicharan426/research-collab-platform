<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/notifications_fn.php';

function addComment($postId, $userId, $commentText, $parentCommentId = null) {
    $pdo = getDBConnection();
    if (empty($commentText)) return "Comment cannot be empty.";
    if (strlen($commentText) > 1000) return "Comment must be less than 1000 characters.";
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text, parent_comment_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$postId, $userId, $commentText, $parentCommentId])) {
        $p = $pdo->prepare("SELECT user_id, title FROM research_posts WHERE post_id = ?");
        $p->execute([$postId]); $post = $p->fetch(PDO::FETCH_ASSOC);
        $s = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
        $s->execute([$userId]); $sender = $s->fetch(PDO::FETCH_ASSOC);
        if ($post && $post['user_id'] != $userId)
            createNotification($post['user_id'], $userId, 'comment', htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' commented on your post "' . htmlspecialchars(substr($post['title'], 0, 40), ENT_QUOTES, 'UTF-8') . '"', "post_details.php?id=$postId");
        // Notify parent comment author if it's a reply
        if ($parentCommentId) {
            $pc = $pdo->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
            $pc->execute([$parentCommentId]); $parentComment = $pc->fetch(PDO::FETCH_ASSOC);
            if ($parentComment && $parentComment['user_id'] != $userId)
                createNotification($parentComment['user_id'], $userId, 'comment', htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' replied to your comment', "post_details.php?id=$postId");
        }
        return "success";
    }
    return "Failed to add comment.";
}

function deleteComment($commentId, $userId, $postAuthorId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE comment_id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$comment) return "Comment not found.";
    // Allow delete if: comment owner OR post author
    if ($comment['user_id'] == $userId || $postAuthorId == $userId) {
        $pdo->prepare("DELETE FROM comments WHERE comment_id = ?")->execute([$commentId]);
        return "success";
    }
    return "Not authorized to delete this comment.";
}

function getCommentsByPost($postId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT c.comment_id, c.comment_text, c.created_at, c.parent_comment_id, c.user_id, u.name as author_name FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.post_id = ? ORDER BY c.created_at ASC");
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
