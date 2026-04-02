<?php
require_once __DIR__ . '/../config/database.php';

function getUserProfile($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT user_id, name, username, email, role, department, class, designation, bio, profile_picture, phone, location, website, linkedin, research_interests, created_at FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateUserProfile($userId, $data) {
    $pdo      = getDBConnection();
    $username = isset($data['username']) ? trim($data['username']) : null;
    if (!empty($username)) {
        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) return "Username must be 3-30 characters (letters, numbers, underscores only).";
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $stmt->execute([$username, $userId]);
        if ($stmt->fetch()) return "Username already taken. Please choose another.";
    } else { $username = null; }
    $website  = isset($data['website'])  ? trim($data['website'])  : null;
    $linkedin = isset($data['linkedin']) ? trim($data['linkedin']) : null;
    if ($website  && !filter_var($website,  FILTER_VALIDATE_URL)) return "Invalid website URL.";
    if ($linkedin && !filter_var($linkedin, FILTER_VALIDATE_URL)) return "Invalid LinkedIn URL.";
    $stmt = $pdo->prepare("UPDATE users SET username=?, bio=?, phone=?, location=?, website=?, linkedin=?, research_interests=? WHERE user_id=?");
    if ($stmt->execute([$username,
        htmlspecialchars(trim($data['bio'] ?? ''), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(trim($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8'),
        htmlspecialchars(trim($data['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
        $website, $linkedin,
        htmlspecialchars(trim($data['research_interests'] ?? ''), ENT_QUOTES, 'UTF-8'),
        $userId])) return "success";
    return "Failed to update profile.";
}

function searchUsers($searchTerm) {
    $pdo  = getDBConnection();
    $like = '%' . $searchTerm . '%';
    $stmt = $pdo->prepare("SELECT user_id, name, username, email, role, department, class, designation, bio FROM users WHERE username LIKE ? OR name LIKE ? OR department LIKE ? ORDER BY username ASC LIMIT 50");
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserPostCount($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM research_posts WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
}

function getUserTotalLikes($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM post_likes pl JOIN research_posts rp ON pl.post_id = rp.post_id WHERE rp.user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
}

function getUserTotalViews($userId) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT SUM(views) as total FROM research_posts WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
}
?>
