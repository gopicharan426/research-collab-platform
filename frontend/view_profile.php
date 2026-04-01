<?php
require_once '../backend/app/auth/auth.php';
require_once '../backend/app/users/profile.php';
require_once '../backend/app/posts/posts.php';
require_once '../backend/app/social/follow.php';
require_once '../backend/app/social/notifications.php';

requireLogin();

if (isset($_GET['logout'])) logoutUser();

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$userId) { header("Location: search_users.php"); exit(); }

$profile = getUserProfile($userId);
if (!$profile) { header("Location: search_users.php"); exit(); }

// Handle follow / unfollow
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'follow') {
        followUser($_SESSION['user_id'], $userId);
    } elseif ($_POST['action'] === 'unfollow') {
        unfollowUser($_SESSION['user_id'], $userId);
    }
    header("Location: view_profile.php?id=$userId");
    exit();
}

$userPosts      = getPostsByUser($userId);
$postCount      = getUserPostCount($userId);
$totalLikes     = getUserTotalLikes($userId);
$totalViews     = getUserTotalViews($userId);
$followerCount  = getFollowerCount($userId);
$followingCount = getFollowingCount($userId);
$isOwnProfile   = ($_SESSION['user_id'] == $userId);
$following      = !$isOwnProfile && isFollowing($_SESSION['user_id'], $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['name']); ?> - Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo"><h1>Research Collaboration Platform</h1></div>
                <nav class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="index.php">Home</a>
                    <a href="search_users.php">Search Users</a>
                    <div class="notif-wrapper">
                        <button class="notif-bell" id="notifBell" onclick="toggleNotifPanel()">
                            🔔
                            <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                        </button>
                        <div class="notif-panel" id="notifPanel">
                            <div class="notif-header">
                                <span>Notifications</span>
                                <button onclick="markAllRead()" class="notif-mark-read">Mark all read</button>
                            </div>
                            <div class="notif-list" id="notifList"><p class="notif-empty">Loading...</p></div>
                        </div>
                    </div>
                    <a href="?logout=1">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <a href="search_users.php" class="btn btn-secondary" style="margin-bottom:1rem;">← Back to Search</a>

            <div class="dashboard-grid">
                <!-- Left: User Info Panel -->
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($profile['name'], 0, 1)); ?></div>
                    <h3><?php echo htmlspecialchars($profile['name']); ?></h3>

                    <?php if (!empty($profile['username'])): ?>
                        <p>@<?php echo htmlspecialchars($profile['username']); ?></p>
                    <?php endif; ?>

                    <p><?php echo htmlspecialchars($profile['email']); ?></p>

                    <?php if (!empty($profile['role'])): ?>
                        <p class="user-meta"><?php echo ucfirst($profile['role']); ?><?php echo !empty($profile['designation']) ? ' · ' . htmlspecialchars($profile['designation']) : ''; ?><?php echo !empty($profile['class']) ? ' · ' . htmlspecialchars($profile['class']) : ''; ?></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['department'])): ?>
                        <p class="user-meta">📚 <?php echo htmlspecialchars($profile['department']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['location'])): ?>
                        <p class="user-meta">📍 <?php echo htmlspecialchars($profile['location']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['phone'])): ?>
                        <p class="user-meta">📞 <?php echo htmlspecialchars($profile['phone']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['website'])): ?>
                        <p class="user-meta">🌐 <a href="<?php echo htmlspecialchars($profile['website']); ?>" target="_blank" style="color:rgba(255,255,255,0.9);"><?php echo htmlspecialchars($profile['website']); ?></a></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['linkedin'])): ?>
                        <p class="user-meta">🔗 <a href="<?php echo htmlspecialchars($profile['linkedin']); ?>" target="_blank" style="color:rgba(255,255,255,0.9);">LinkedIn</a></p>
                    <?php endif; ?>

                    <?php if (!empty($profile['bio'])): ?>
                        <div class="user-bio"><?php echo nl2br(htmlspecialchars(substr($profile['bio'], 0, 120))); ?><?php echo strlen($profile['bio']) > 120 ? '...' : ''; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($profile['research_interests'])): ?>
                        <p class="user-meta" style="margin-top:8px;">🔬 <?php echo htmlspecialchars(substr($profile['research_interests'], 0, 80)); ?><?php echo strlen($profile['research_interests']) > 80 ? '...' : ''; ?></p>
                    <?php endif; ?>

                    <p class="user-meta" style="margin-top:8px;">🗓 Member since <?php echo date('F Y', strtotime($profile['created_at'])); ?></p>

                    <!-- Stats: Posts / Followers / Following / Likes / Views -->
                    <div class="user-stats" style="flex-wrap:wrap; gap:12px;">
                        <div><strong><?php echo $postCount; ?></strong><span>Posts</span></div>
                        <div><strong><?php echo $followerCount; ?></strong><span>Followers</span></div>
                        <div><strong><?php echo $followingCount; ?></strong><span>Following</span></div>
                        <div><strong><?php echo $totalLikes; ?></strong><span>Likes</span></div>
                        <div><strong><?php echo $totalViews; ?></strong><span>Views</span></div>
                    </div>

                    <!-- Follow / Unfollow Button -->
                    <?php if (!$isOwnProfile): ?>
                        <form method="POST" style="margin-top:1rem;">
                            <input type="hidden" name="action" value="<?php echo $following ? 'unfollow' : 'follow'; ?>">
                            <button type="submit" class="btn <?php echo $following ? 'btn-unfollow' : 'btn-follow'; ?>" style="width:100%;">
                                <?php echo $following ? '✓ Following' : '+ Follow'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="edit_profile.php" class="btn btn-secondary" style="margin-top:1rem; width:100%;">Edit Profile</a>
                    <?php endif; ?>
                </div>

                <!-- Right: Posts -->
                <div>
                    <div class="card">
                        <h3>Research Posts (<?php echo count($userPosts); ?>)</h3>
                        <?php if (empty($userPosts)): ?>
                            <p>No posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($userPosts as $post): ?>
                                <div class="post-card">
                                    <h4 class="post-title">
                                        <a href="post_details.php?id=<?php echo $post['post_id']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h4>
                                    <div class="post-meta">
                                        Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                        • <?php echo $post['views']; ?> views
                                    </div>
                                    <div class="post-description">
                                        <?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 200))); ?>
                                        <?php if (strlen($post['description']) > 200): ?>...<?php endif; ?>
                                    </div>
                                    <div class="post-actions">
                                        <a href="post_details.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Read More</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Research Collaboration Platform.</p>
        </div>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
