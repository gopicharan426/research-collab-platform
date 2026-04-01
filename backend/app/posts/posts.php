<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../social/notifications.php';

function createPost($userId, $title, $description) {
    if (empty($title) || empty($description)) return "Title and description are required.";
    if (strlen($title) > 200) return "Title must be less than 200 characters.";

    $col    = getCollection('research_posts');
    $postId = getNextId('research_posts');

    $col->insertOne([
        'post_id'     => $postId,
        'user_id'     => (int)$userId,
        'title'       => $title,
        'description' => $description,
        'views'       => 0,
        'created_at'  => date('Y-m-d H:i:s')
    ]);

    // Notify followers
    $users  = getCollection('users');
    $author = $users->findOne(['user_id' => (int)$userId]);
    if ($author) notifyFollowersNewPost($userId, $postId, $author['name'], $title);

    return "success";
}

function getAllPosts($search = '', $currentUserId = 0) {
    $col      = getCollection('research_posts');
    $users    = getCollection('users');
    $followers = getCollection('followers');

    $filter = [];
    if (!empty($search)) {
        $filter = ['$or' => [
            ['title'       => new MongoDB\BSON\Regex($search, 'i')],
            ['description' => new MongoDB\BSON\Regex($search, 'i')]
        ]];
    }

    $posts = $col->find($filter, ['sort' => ['created_at' => -1]])->toArray();

    // Get followed user IDs
    $followedIds = [];
    if ($currentUserId) {
        $follows = $followers->find(['follower_id' => (int)$currentUserId])->toArray();
        foreach ($follows as $f) $followedIds[] = (int)$f['following_id'];
    }

    $result = [];
    foreach ($posts as $post) {
        $author = $users->findOne(['user_id' => (int)$post['user_id']]);
        $result[] = [
            'post_id'          => (int)$post['post_id'],
            'title'            => $post['title'],
            'description'      => $post['description'],
            'created_at'       => $post['created_at'],
            'views'            => (int)($post['views'] ?? 0),
            'author_name'      => $author['name'] ?? 'Unknown',
            'author_id'        => (int)$post['user_id'],
            'author_username'  => $author['username'] ?? '',
            'feed_priority'    => in_array((int)$post['user_id'], $followedIds) ? 0 : 1
        ];
    }

    // Sort: followed first, then by date
    usort($result, fn($a, $b) =>
        $a['feed_priority'] !== $b['feed_priority']
            ? $a['feed_priority'] - $b['feed_priority']
            : strcmp($b['created_at'], $a['created_at'])
    );

    return $result;
}

function getPostById($postId) {
    $col   = getCollection('research_posts');
    $users = getCollection('users');
    $post  = $col->findOne(['post_id' => (int)$postId]);
    if (!$post) return null;
    $author = $users->findOne(['user_id' => (int)$post['user_id']]);
    return [
        'post_id'     => (int)$post['post_id'],
        'title'       => $post['title'],
        'description' => $post['description'],
        'created_at'  => $post['created_at'],
        'views'       => (int)($post['views'] ?? 0),
        'user_id'     => (int)$post['user_id'],
        'author_name' => $author['name'] ?? 'Unknown'
    ];
}

function getPostsByUser($userId) {
    $col   = getCollection('research_posts');
    $posts = $col->find(['user_id' => (int)$userId], ['sort' => ['created_at' => -1]])->toArray();
    return array_map(fn($p) => [
        'post_id'     => (int)$p['post_id'],
        'title'       => $p['title'],
        'description' => $p['description'],
        'created_at'  => $p['created_at'],
        'views'       => (int)($p['views'] ?? 0)
    ], $posts);
}

function deletePost($postId, $userId, $isAdmin = false) {
    $col  = getCollection('research_posts');
    $post = $col->findOne(['post_id' => (int)$postId]);
    if (!$post) return "Post not found.";
    if ($isAdmin || (int)$post['user_id'] === (int)$userId) {
        $col->deleteOne(['post_id' => (int)$postId]);
        return "success";
    }
    return "Failed to delete post.";
}
?>
