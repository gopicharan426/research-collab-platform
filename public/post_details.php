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

// Get post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header("Location: browse.php");
    exit();
}

// Increment view count
incrementViews($postId);

// Get post details
$post = getPostById($postId);

if (!$post) {
    header("Location: browse.php");
    exit();
}

// Handle like toggle
if (isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
    toggleLike($postId, $_SESSION['user_id']);
    header("Location: post_details.php?id=$postId");
    exit();
}

// Handle comment submission
$message = '';
$messageType = '';

if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $result = addComment($postId, $_SESSION['user_id'], $_POST['comment_text']);
    if ($result === 'success') {
        $message = 'Comment added successfully!';
        $messageType = 'success';
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

// Get comments for this post
$comments = getCommentsByPost($postId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Research Collaboration Platform</title>
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
                    <a href="?logout=1">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <a href="browse.php" class="btn btn-secondary" style="margin-bottom: 1rem;">← Back to Browse</a>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Post Details -->
            <div class="card">
                <h1 style="color: #495057; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                <div class="post-meta" style="margin-bottom: 2rem;">
                    By <?php echo htmlspecialchars($post['author_name']); ?> on <?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
                    • <?php echo getViewCount($postId); ?> views
                    • <?php echo getLikeCount($postId); ?> likes
                </div>
                <div class="post-description" style="font-size: 1.1rem; line-height: 1.7;">
                    <?php echo nl2br(htmlspecialchars($post['description'])); ?>
                </div>
                
                <!-- Like Button -->
                <form method="POST" style="margin-top: 2rem;">
                    <input type="hidden" name="action" value="toggle_like">
                    <button type="submit" class="btn <?php echo hasUserLiked($postId, $_SESSION['user_id']) ? 'btn-secondary' : 'btn-primary'; ?>">
                        <?php echo hasUserLiked($postId, $_SESSION['user_id']) ? '❤️ Unlike' : '❤️ Like'; ?> (<?php echo getLikeCount($postId); ?>)
                    </button>
                </form>
            </div>

            <!-- Add Comment Form -->
            <div class="card">
                <h2>Add Your Comment</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_comment">
                    
                    <div class="form-group">
                        <label for="comment_text">Your Comment:</label>
                        <textarea id="comment_text" name="comment_text" class="form-control" maxlength="1000" required placeholder="Share your thoughts, suggestions, or questions about this research..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Comment</button>
                </form>
            </div>

            <!-- Comments Section -->
            <div class="card">
                <h2>Comments (<?php echo count($comments); ?>)</h2>
                
                <?php if (empty($comments)): ?>
                    <p>No comments yet. Be the first to share your thoughts!</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-author">
                                <?php echo htmlspecialchars($comment['author_name']); ?>
                            </div>
                            <div class="comment-text">
                                <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                            </div>
                            <div class="comment-date">
                                <?php echo date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
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