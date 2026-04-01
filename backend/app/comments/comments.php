<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../social/notifications.php';

function addComment($postId, $userId, $commentText) {
    if (empty($commentText)) return "Comment cannot be empty.";
    if (strlen($commentText) > 1000) return "Comment must be less than 1000 characters.";

    $col       = getCollection('comments');
    $posts     = getCollection('research_posts');
    $users     = getCollection('users');
    $commentId = getNextId('comments');

    $col->insertOne([
        'comment_id'   => $commentId,
        'post_id'      => (int)$postId,
        'user_id'      => (int)$userId,
        'comment_text' => $commentText,
        'created_at'   => date('Y-m-d H:i:s')
    ]);

    $post   = $posts->findOne(['post_id' => (int)$postId]);
    $sender = $users->findOne(['user_id' => (int)$userId]);

    if ($post && (int)$post['user_id'] !== (int)$userId) {
        createNotification(
            (int)$post['user_id'], (int)$userId, 'comment',
            htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . ' commented on your post "' . htmlspecialchars(substr($post['title'], 0, 40), ENT_QUOTES, 'UTF-8') . '"',
            "/post_details.php?id=$postId"
        );
    }
    return "success";
}

function getCommentsByPost($postId) {
    $col      = getCollection('comments');
    $users    = getCollection('users');
    $comments = $col->find(['post_id' => (int)$postId], ['sort' => ['created_at' => 1]])->toArray();
    return array_map(function($c) use ($users) {
        $author = $users->findOne(['user_id' => (int)$c['user_id']]);
        return [
            'comment_text' => $c['comment_text'],
            'created_at'   => $c['created_at'],
            'author_name'  => $author['name'] ?? 'Unknown'
        ];
    }, $comments);
}

function getCommentCount($postId) {
    return (int)getCollection('comments')->countDocuments(['post_id' => (int)$postId]);
}
?>
