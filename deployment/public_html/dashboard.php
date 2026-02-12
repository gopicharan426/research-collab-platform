<?php
require_once '../app/auth/auth.php';
require_once '../app/posts/posts.php';

// Require login
requireLogin();

// Handle logout
if (isset($_GET['logout'])) {
    logoutUser();
}

// Handle new post creation
$message = '';
$messageType = '';

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
                    <a href="browse.php">Browse Research</a>
                    <a href="?logout=1" onclick="return confirmLogout()">Logout</a>
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
                    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                    <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    <p><strong><?php echo count($userPosts); ?></strong> Research Posts</p>
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
                            </div>
                            <div class="post-description">
                                <?php echo nl2br(htmlspecialchars(substr($post['description'], 0, 200))); ?>
                                <?php if (strlen($post['description']) > 200): ?>...<?php endif; ?>
                            </div>
                            <a href="post_details.php?id=<?php echo $post['post_id']; ?>" class="btn btn-secondary">View Details</a>
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