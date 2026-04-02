<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/profile.php';

requireLogin();

if (isset($_GET['logout'])) {
    logoutUser();
}

$message = '';
$messageType = '';

if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $data = [
        'username' => $_POST['username'] ?? '',
        'bio' => $_POST['bio'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'location' => $_POST['location'] ?? '',
        'website' => $_POST['website'] ?? '',
        'linkedin' => $_POST['linkedin'] ?? '',
        'research_interests' => $_POST['research_interests'] ?? ''
    ];
    
    $result = updateUserProfile($_SESSION['user_id'], $data);
    
    if ($result === 'success') {
        $_SESSION['user_username'] = trim($_POST['username'] ?? '');
    }
    
    if ($result === 'success') {
        $message = 'Profile updated successfully!';
        $messageType = 'success';
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

$profile = getUserProfile($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>Edit Your Profile</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($profile['username'] ?? ''); ?>" pattern="[a-zA-Z0-9_]{3,30}" placeholder="e.g., john_doe123">
                        <small style="color: #6c757d;">3-30 characters, letters, numbers, underscores only</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio:</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4" maxlength="500"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="research_interests">Research Interests:</label>
                        <textarea id="research_interests" name="research_interests" class="form-control" rows="3"><?php echo htmlspecialchars($profile['research_interests'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="website">Website:</label>
                        <input type="url" id="website" name="website" class="form-control" value="<?php echo htmlspecialchars($profile['website'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="linkedin">LinkedIn:</label>
                        <input type="url" id="linkedin" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($profile['linkedin'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Research Collaboration Platform.</p>
        </div>
    </footer>
</body>
</html>
