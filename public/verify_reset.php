<?php
require_once '../app/config/database.php';

$message = '';
$messageType = '';
$validToken = false;
$email = '';

// Verify token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT email, reset_token_expiry FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $expiry = strtotime($user['reset_token_expiry']);
        $now = time();
        
        if ($expiry > $now) {
            $validToken = true;
            $email = $user['email'];
        } else {
            $message = 'This reset link has expired. Please request a new one.';
            $messageType = 'error';
        }
    } else {
        $message = 'Invalid reset link.';
        $messageType = 'error';
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
        $validToken = true;
        $email = $_POST['email'];
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters.';
        $messageType = 'error';
        $validToken = true;
        $email = $_POST['email'];
    } else {
        $pdo = getDBConnection();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        
        if ($stmt->execute([$hashedPassword, $token])) {
            $message = 'Password reset successful! You can now login with your new password.';
            $messageType = 'success';
            $validToken = false;
        } else {
            $message = 'Failed to reset password. Please try again.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Research Collaboration Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Research Collaboration Platform</h1>
                </div>
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

            <div style="max-width: 450px; margin: 0 auto;">
                <?php if ($validToken): ?>
                    <div class="card">
                        <h2 style="text-align: center;">Create New Password</h2>
                        <p style="text-align: center; color: #64748b; margin-bottom: 2rem;">
                            Enter your new password below
                        </p>
                        
                        <form method="POST">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                                <small style="color: #64748b;">Minimum 6 characters</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <h2 style="text-align: center;">Reset Link Status</h2>
                        <?php if ($messageType === 'success'): ?>
                            <p style="text-align: center; color: #10b981; margin-bottom: 2rem;">
                                ✓ Your password has been reset successfully!
                            </p>
                        <?php else: ?>
                            <p style="text-align: center; color: #ef4444; margin-bottom: 2rem;">
                                ✗ This reset link is invalid or has expired.
                            </p>
                        <?php endif; ?>
                        
                        <a href="index.php" class="btn btn-primary btn-block">Go to Login</a>
                    </div>
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