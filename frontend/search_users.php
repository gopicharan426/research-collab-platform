<?php
require_once '../backend/app/auth/auth.php';
require_once '../backend/app/users/profile.php';

requireLogin();

if (isset($_GET['logout'])) {
    logoutUser();
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$users = [];

if (!empty($searchTerm)) {
    $users = searchUsers($searchTerm);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Users</title>
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
                    <a href="?logout=1">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="card">
                <h2>Search Users</h2>
                <form method="GET">
                    <div class="form-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by username, name, or department..." value="<?php echo htmlspecialchars($searchTerm); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($searchTerm): ?>
                        <a href="search_users.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($searchTerm): ?>
                <?php if (empty($users)): ?>
                    <div class="card">
                        <p>No users found for "<?php echo htmlspecialchars($searchTerm); ?>"</p>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h3>Search Results (<?php echo count($users); ?> users found)</h3>
                    </div>
                    
                    <?php foreach ($users as $user): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                            <?php if ($user['username']): ?>
                                <p style="color: #6c757d;">@<?php echo htmlspecialchars($user['username']); ?></p>
                            <?php endif; ?>
                            <p><strong>Role:</strong> <?php echo ucfirst($user['role'] ?? 'N/A'); ?></p>
                            <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></p>
                            <?php if ($user['role'] === 'student' && $user['class']): ?>
                                <p><strong>Class:</strong> <?php echo htmlspecialchars($user['class']); ?></p>
                            <?php elseif ($user['role'] === 'professor' && $user['designation']): ?>
                                <p><strong>Designation:</strong> <?php echo htmlspecialchars($user['designation']); ?></p>
                            <?php endif; ?>
                            <?php if ($user['bio']): ?>
                                <p><?php echo nl2br(htmlspecialchars(substr($user['bio'], 0, 150))); ?><?php echo strlen($user['bio']) > 150 ? '...' : ''; ?></p>
                            <?php endif; ?>
                            <a href="view_profile.php?id=<?php echo $user['user_id']; ?>" class="btn btn-primary">View Profile</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Research Collaboration Platform.</p>
        </div>
    </footer>
</body>
</html>
