<?php
require_once '../app/auth/auth.php';
require_once '../app/posts/posts.php';
require_once '../app/comments/comments.php';
require_once '../app/posts/likes.php';

// Require login
requireLogin();

// Handle logout
if (isset($_GET['logout'])) {
    logoutUser();
}

// Get all posts
$search = isset($_GET['search']) ? $_GET['search'] : '';
$allPosts = getAllPosts($search);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Research - Research Collaboration Platform</title>
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
                    <a href="?logout=1">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="card">
                <h2>All Research Posts</h2>
                <p>Discover and collaborate on research ideas from the community.</p>
                
                <!-- Search Bar -->
                <form method="GET" style="margin-top: 1.5rem;">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search research posts..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search): ?>
                        <a href="index.php" class="btn btn-secondary">Clear Search</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (empty($allPosts)): ?>
                <div class="card">
                    <?php if ($search): ?>
                        <p>No research posts found for "<?php echo htmlspecialchars($search); ?>". <a href="index.php">View all posts</a></p>
                    <?php else: ?>
                        <p>No research posts available yet. Be the first to <a href="dashboard.php">create a post</a>!</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($allPosts as $post): ?>
                    <div class="post-card">
                        <h3 class="post-title">
                            <a href="post_details.php?id=<?php echo $post['post_id']; ?>" style="text-decoration: none; color: inherit;">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        <div class="post-meta">
                            By <?php echo htmlspecialchars($post['author_name']); ?> on <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            • <?php echo getCommentCount($post['post_id']); ?> comments
                            • <?php echo getViewCount($post['post_id']); ?> views
                            • <?php echo getLikeCount($post['post_id']); ?> likes
                        </div>
                        <div class="post-description">
                            <?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 300))); ?>
                            <?php if (strlen($post['description']) > 300): ?>...<?php endif; ?>
                        </div>
                            <div class="post-actions">
                                <a href="post_details.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Read More & Comment</a>
                                <?php if (isAdmin()): ?>
                                    <a href="dashboard.php?delete=1&post_id=<?php echo $post['post_id']; ?>" class="btn btn-secondary" style="background: var(--danger);" onclick="return confirm('Admin: Delete this post?')">Delete Post</a>
                                <?php endif; ?>
                            </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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