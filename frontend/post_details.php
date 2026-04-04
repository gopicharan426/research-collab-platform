<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/posts.php';
require_once __DIR__ . '/comments.php';
require_once __DIR__ . '/likes.php';
require_once __DIR__ . '/notifications_fn.php';

requireLogin();

if (isset($_GET['logout'])) logoutUser();

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$postId) { header("Location: index.php"); exit(); }

$post = getPostById($postId);
if (!$post) { header("Location: index.php"); exit(); }

$message = '';
$messageType = '';

// Handle like toggle
if (isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
    toggleLike($postId, $_SESSION['user_id']);
    header("Location: post_details.php?id=$postId");
    exit();
}

// Handle comment submission
if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $parentId = isset($_POST['parent_comment_id']) && (int)$_POST['parent_comment_id'] > 0 ? (int)$_POST['parent_comment_id'] : null;
    $result = addComment($postId, $_SESSION['user_id'], $_POST['comment_text'], $parentId);
    if ($result === 'success') {
        header("Location: post_details.php?id=$postId");
        exit();
    }
    $message = $result;
    $messageType = 'error';
}

// Handle delete comment
if (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
    $commentId = (int)$_POST['comment_id'];
    $result = deleteComment($commentId, $_SESSION['user_id'], $post['user_id']);
    if ($result === 'success') {
        header("Location: post_details.php?id=$postId");
        exit();
    }
    $message = $result;
    $messageType = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') incrementViews($postId);

$allComments = getCommentsByPost($postId);

// Organize comments: top-level and replies
$topComments = [];
$replies = [];
foreach ($allComments as $c) {
    if ($c['parent_comment_id']) {
        $replies[$c['parent_comment_id']][] = $c;
    } else {
        $topComments[] = $c;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Research Collaboration Platform</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .reply-form { display:none; margin-top:0.75rem; }
        .replies { margin-left:2rem; border-left:3px solid var(--border); padding-left:1rem; margin-top:0.75rem; }
        .comment-actions { margin-top:0.5rem; display:flex; gap:0.5rem; }
        .btn-reply { background:none; border:none; color:var(--primary); cursor:pointer; font-size:0.85rem; padding:0; }
        .btn-delete-comment { background:none; border:none; color:#dc3545; cursor:pointer; font-size:0.85rem; padding:0; }
    </style>
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
            <a href="index.php" class="btn btn-secondary" style="margin-bottom:1rem;">← Back to Home</a>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Post Details -->
            <div class="card">
                <h1 style="color:#495057;margin-bottom:1rem;"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="post-meta" style="margin-bottom:2rem;">
                    By <?php echo htmlspecialchars($post['author_name']); ?> on <?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
                    • <?php echo getViewCount($postId); ?> views
                    • <?php echo getLikeCount($postId); ?> likes
                </div>
                <div class="post-description" style="font-size:1.1rem;line-height:1.7;">
                    <?php echo nl2br(htmlspecialchars($post['description'])); ?>
                </div>
                <form method="POST" style="margin-top:2rem;">
                    <input type="hidden" name="action" value="toggle_like">
                    <button type="submit" class="btn <?php echo hasUserLiked($postId, $_SESSION['user_id']) ? 'btn-secondary' : 'btn-primary'; ?>">
                        <?php echo hasUserLiked($postId, $_SESSION['user_id']) ? '❤️ Unlike' : '❤️ Like'; ?> (<?php echo getLikeCount($postId); ?>)
                    </button>
                    <?php if (isAdmin()): ?>
                        <a href="dashboard.php?delete=1&post_id=<?php echo $postId; ?>" class="btn btn-secondary" style="background:var(--danger);" onclick="return confirm('Admin: Delete this post?')">Delete Post</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Add Comment Form -->
            <div class="card">
                <h2>Add Your Comment</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_comment">
                    <input type="hidden" name="parent_comment_id" value="0">
                    <div class="form-group">
                        <textarea name="comment_text" class="form-control" maxlength="1000" required placeholder="Share your thoughts..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Comment</button>
                </form>
            </div>

            <!-- Comments Section -->
            <div class="card">
                <h2>Comments (<?php echo count($allComments); ?>)</h2>
                <?php if (empty($topComments)): ?>
                    <p>No comments yet. Be the first to share your thoughts!</p>
                <?php else: ?>
                    <?php foreach ($topComments as $comment): ?>
                        <div class="comment" id="comment-<?php echo $comment['comment_id']; ?>">
                            <div class="comment-author"><?php echo htmlspecialchars($comment['author_name']); ?></div>
                            <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                            <div class="comment-date"><?php echo date('F j, Y \a\t g:i A', strtotime($comment['created_at'])); ?></div>
                            <div class="comment-actions">
                                <button class="btn-reply" onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">💬 Reply</button>
                                <?php if ($comment['user_id'] == $_SESSION['user_id'] || $post['user_id'] == $_SESSION['user_id']): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this comment?')">
                                        <input type="hidden" name="action" value="delete_comment">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                                        <button type="submit" class="btn-delete-comment">🗑️ Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <!-- Reply Form -->
                            <div class="reply-form" id="reply-form-<?php echo $comment['comment_id']; ?>">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_comment">
                                    <input type="hidden" name="parent_comment_id" value="<?php echo $comment['comment_id']; ?>">
                                    <div class="form-group">
                                        <textarea name="comment_text" class="form-control" maxlength="1000" required placeholder="Write a reply..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="font-size:0.85rem;padding:0.4rem 1rem;">Post Reply</button>
                                    <button type="button" class="btn btn-secondary" style="font-size:0.85rem;padding:0.4rem 1rem;" onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">Cancel</button>
                                </form>
                            </div>

                            <!-- Replies -->
                            <?php if (!empty($replies[$comment['comment_id']])): ?>
                                <div class="replies">
                                    <?php foreach ($replies[$comment['comment_id']] as $reply): ?>
                                        <div class="comment" id="comment-<?php echo $reply['comment_id']; ?>">
                                            <div class="comment-author"><?php echo htmlspecialchars($reply['author_name']); ?></div>
                                            <div class="comment-text"><?php echo nl2br(htmlspecialchars($reply['comment_text'])); ?></div>
                                            <div class="comment-date"><?php echo date('F j, Y \a\t g:i A', strtotime($reply['created_at'])); ?></div>
                                            <div class="comment-actions">
                                                <?php if ($reply['user_id'] == $_SESSION['user_id'] || $post['user_id'] == $_SESSION['user_id']): ?>
                                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this reply?')">
                                                        <input type="hidden" name="action" value="delete_comment">
                                                        <input type="hidden" name="comment_id" value="<?php echo $reply['comment_id']; ?>">
                                                        <button type="submit" class="btn-delete-comment">🗑️ Delete</button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
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
    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById('reply-form-' + commentId);
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
