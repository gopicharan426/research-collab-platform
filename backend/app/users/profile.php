<?php
require_once __DIR__ . '/../config/database.php';

function getUserProfile($userId) {
    $user = getCollection('users')->findOne(['user_id' => (int)$userId]);
    if (!$user) return null;
    return [
        'user_id'            => (int)$user['user_id'],
        'name'               => $user['name'] ?? '',
        'username'           => $user['username'] ?? '',
        'email'              => $user['email'] ?? '',
        'role'               => $user['role'] ?? '',
        'department'         => $user['department'] ?? '',
        'class'              => $user['class'] ?? '',
        'designation'        => $user['designation'] ?? '',
        'bio'                => $user['bio'] ?? '',
        'profile_picture'    => $user['profile_picture'] ?? '',
        'phone'              => $user['phone'] ?? '',
        'location'           => $user['location'] ?? '',
        'website'            => $user['website'] ?? '',
        'linkedin'           => $user['linkedin'] ?? '',
        'research_interests' => $user['research_interests'] ?? '',
        'created_at'         => $user['created_at'] ?? ''
    ];
}

function updateUserProfile($userId, $data) {
    $col      = getCollection('users');
    $username = isset($data['username']) ? trim($data['username']) : null;

    if (!empty($username)) {
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username))
            return "Username must be 3-30 characters (letters, numbers, underscores only).";
        $existing = $col->findOne(['username' => $username, 'user_id' => ['$ne' => (int)$userId]]);
        if ($existing) return "Username already taken. Please choose another.";
    } else {
        $username = null;
    }

    $website  = isset($data['website'])  ? trim($data['website'])  : null;
    $linkedin = isset($data['linkedin']) ? trim($data['linkedin']) : null;
    if ($website  && !filter_var($website,  FILTER_VALIDATE_URL)) return "Invalid website URL.";
    if ($linkedin && !filter_var($linkedin, FILTER_VALIDATE_URL)) return "Invalid LinkedIn URL.";

    $col->updateOne(['user_id' => (int)$userId], ['$set' => [
        'username'           => $username,
        'bio'                => htmlspecialchars(trim($data['bio'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'phone'              => htmlspecialchars(trim($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'location'           => htmlspecialchars(trim($data['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
        'website'            => $website,
        'linkedin'           => $linkedin,
        'research_interests' => htmlspecialchars(trim($data['research_interests'] ?? ''), ENT_QUOTES, 'UTF-8')
    ]]);
    return "success";
}

function searchUsers($searchTerm) {
    $regex = new MongoDB\BSON\Regex($searchTerm, 'i');
    $users = getCollection('users')->find([
        '$or' => [['username' => $regex], ['name' => $regex], ['department' => $regex]]
    ], ['sort' => ['username' => 1], 'limit' => 50])->toArray();

    return array_map(fn($u) => [
        'user_id'     => (int)$u['user_id'],
        'name'        => $u['name'] ?? '',
        'username'    => $u['username'] ?? '',
        'email'       => $u['email'] ?? '',
        'role'        => $u['role'] ?? '',
        'department'  => $u['department'] ?? '',
        'class'       => $u['class'] ?? '',
        'designation' => $u['designation'] ?? '',
        'bio'         => $u['bio'] ?? ''
    ], $users);
}

function getUserPostCount($userId) {
    return (int)getCollection('research_posts')->countDocuments(['user_id' => (int)$userId]);
}

function getUserTotalLikes($userId) {
    $posts    = getCollection('research_posts')->find(['user_id' => (int)$userId])->toArray();
    $postIds  = array_map(fn($p) => (int)$p['post_id'], $posts);
    if (empty($postIds)) return 0;
    return (int)getCollection('post_likes')->countDocuments(['post_id' => ['$in' => $postIds]]);
}

function getUserTotalViews($userId) {
    $posts = getCollection('research_posts')->find(['user_id' => (int)$userId])->toArray();
    return array_sum(array_map(fn($p) => (int)($p['views'] ?? 0), $posts));
}
?>
