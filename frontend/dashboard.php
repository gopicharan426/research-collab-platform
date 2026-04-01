<?php
require_once '../backend/app/auth/auth.php';
require_once '../backend/app/posts/posts.php';
require_once '../backend/app/posts/likes.php';
require_once '../backend/app/users/profile.php';
require_once '../backend/app/social/notifications.php';

// Require login
requireLogin();

// Handle logout
if (isset($_GET['logout'])) {
    logoutUser();
}

// Handle delete post
if (isset($_GET['delete']) && isset($_GET['post_id'])) {
    $result = deletePost($_GET['post_id'], $_SESSION['user_id'], isAdmin());
    if ($result === 'success') {
        header("Location: dashboard.php?deleted=1");
        exit();
    }
}

// Handle new post creation
$message = '';
$messageType = '';

if (isset($_GET['deleted'])) {
    $message = 'Post deleted successfully!';
    $messageType = 'success';
}

if (isset($_POST['action']) && $_POST['action'] === 'create_post') {
    $result = createPost($_SESSION['user_id'], $_POST['title'], $_POST['description']);
    if ($result === 'success') {
        $message = 'Research post created successfully!';
        $messageType = 'success';
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

// Get user's posts
$userPosts = getPostsByUser($_SESSION['user_id']);
$profile = getUserProfile($_SESSION['user_id']);
$totalLikes = getUserTotalLikes($_SESSION['user_id']);
$totalViews = getUserTotalViews($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Research Collaboration Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Research Collaboration Platform</h1>
                </div>
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
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-grid">
                <!-- User Profile -->
            <div class="user-info">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
                <h3><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                
                <?php if (!empty($profile['username'])): ?>
                    <p>@<?php echo htmlspecialchars($profile['username']); ?></p>
                <?php endif; ?>
                
                <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                
                <?php if (!empty($profile['role'])): ?>
                    <p class="user-meta"><?php echo ucfirst($profile['role']); ?><?php echo !empty($profile['designation']) ? ' · ' . htmlspecialchars($profile['designation']) : ''; ?><?php echo !empty($profile['class']) ? ' · ' . htmlspecialchars($profile['class']) : ''; ?></p>
                <?php endif; ?>
                
                <?php if (!empty($profile['department'])): ?>
                    <p class="user-meta">📚 <?php echo htmlspecialchars($profile['department']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($profile['location'])): ?>
                    <p class="user-meta">📍 <?php echo htmlspecialchars($profile['location']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($profile['bio'])): ?>
                    <div class="user-bio"><?php echo nl2br(htmlspecialchars(substr($profile['bio'], 0, 120))); ?><?php echo strlen($profile['bio']) > 120 ? '...' : ''; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($profile['research_interests'])): ?>
                    <p class="user-meta" style="margin-top:8px;">🔬 <?php echo htmlspecialchars(substr($profile['research_interests'], 0, 80)); ?><?php echo strlen($profile['research_interests']) > 80 ? '...' : ''; ?></p>
                <?php endif; ?>
                
                <div class="user-stats">
                    <div>
                        <strong><?php echo count($userPosts); ?></strong>
                        <span>Posts</span>
                    </div>
                    <div>
                        <strong><?php echo $totalLikes; ?></strong>
                        <span>Likes</span>
                    </div>
                    <div>
                        <strong><?php echo $totalViews; ?></strong>
                        <span>Views</span>
                    </div>
                </div>
                
                <a href="edit_profile.php" class="btn btn-secondary" style="margin-top: 1rem; width: 100%;">Edit Profile</a>
            </div>

                <!-- Create New Post -->
                <div class="card">
                    <h2>Create New Research Post</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create_post">
                        
                        <div class="form-group">
                            <label for="title">Research Title:</label>
                            <input type="text" id="title" name="title" class="form-control" maxlength="200" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Research Description:</label>
                            <textarea id="description" name="description" class="form-control" maxlength="2000" required placeholder="Describe your research idea, methodology, objectives, and how others can contribute..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Create Post</button>
                    </form>
                </div>
            </div>

            <!-- User's Posts -->
            <div class="card">
                <h2>Your Research Posts</h2>
                <?php if (empty($userPosts)): ?>
                    <p>You haven't created any research posts yet. Create your first post above!</p>
                <?php else: ?>
                    <?php foreach ($userPosts as $post): ?>
                        <div class="post-card">
                            <h3 class="post-title">
                                <a href="post_details.php?id=<?php echo $post['post_id']; ?>" style="text-decoration: none; color: inherit;">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <div class="post-meta">
                                Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                • <?php echo $post['views']; ?> views
                                • <?php echo getLikeCount($post['post_id']); ?> likes
                            </div>
                            <div class="post-description">
                                <?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 200))); ?>
                                <?php if (strlen($post['description']) > 200): ?>...<?php endif; ?>
                            </div>
                            <div class="post-actions">
                                <a href="post_details.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">View Details</a>
                                <a href="?delete=1&post_id=<?php echo $post['post_id']; ?>" class="btn btn-secondary" style="background: var(--danger);">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Research Collaboration Platform. Built for academic purposes.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
