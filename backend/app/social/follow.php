<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/notifications.php';

function followUser($followerId, $followingId) {
    if ($followerId == $followingId) return "Cannot follow yourself.";
    $col      = getCollection('followers');
    $existing = $col->findOne(['follower_id' => (int)$followerId, 'following_id' => (int)$followingId]);
    if ($existing) return "success";

    $col->insertOne([
        'follower_id'  => (int)$followerId,
        'following_id' => (int)$followingId,
        'created_at'   => date('Y-m-d H:i:s')
    ]);

    $sender = getCollection('users')->findOne(['user_id' => (int)$followerId]);
    createNotification(
        (int)$followingId, (int)$followerId, 'follow',
        htmlspecialchars($sender['name'], ENT_QUOTES, 'UTF-8') . " started following you.",
        "/view_profile.php?id=$followerId"
    );
    return "success";
}

function unfollowUser($followerId, $followingId) {
    getCollection('followers')->deleteOne(['follower_id' => (int)$followerId, 'following_id' => (int)$followingId]);
    return "success";
}

function isFollowing($followerId, $followingId) {
    return (bool)getCollection('followers')->findOne(['follower_id' => (int)$followerId, 'following_id' => (int)$followingId]);
}

function getFollowers($userId) {
    $col     = getCollection('followers');
    $users   = getCollection('users');
    $follows = $col->find(['following_id' => (int)$userId])->toArray();
    return array_map(function($f) use ($users) {
        $u = $users->findOne(['user_id' => (int)$f['follower_id']]);
        return ['user_id' => (int)$f['follower_id'], 'name' => $u['name'] ?? '', 'username' => $u['username'] ?? ''];
    }, $follows);
}

function getFollowing($userId) {
    $col     = getCollection('followers');
    $users   = getCollection('users');
    $follows = $col->find(['follower_id' => (int)$userId])->toArray();
    return array_map(function($f) use ($users) {
        $u = $users->findOne(['user_id' => (int)$f['following_id']]);
        return ['user_id' => (int)$f['following_id'], 'name' => $u['name'] ?? '', 'username' => $u['username'] ?? ''];
    }, $follows);
}

function getFollowerCount($userId) {
    return (int)getCollection('followers')->countDocuments(['following_id' => (int)$userId]);
}

function getFollowingCount($userId) {
    return (int)getCollection('followers')->countDocuments(['follower_id' => (int)$userId]);
}
?>
