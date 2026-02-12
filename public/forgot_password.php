<?php
require_once '../app/config/database.php';
require_once '../app/config/email_config.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    if (empty($email)) {
        $message = 'Email is required.';
        $messageType = 'error';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate reset token
            $token = generateResetToken();
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save token to database
            $updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $updateStmt->execute([$token, $expiry, $email]);
            
            // Send email
            if (sendPasswordResetEmail($email, $token)) {
                $message = 'Password reset link has been sent to your email. Please check your inbox.';
                $messageType = 'success';
            } else {
                $message = 'Failed to send email. Please try again later.';
                $messageType = 'error';
            }
        } else {
            // Don't reveal if email exists or not (security best practice)
            $message = 'If an account exists with this email, you will receive a password reset link.';
            $messageType = 'success';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Research Collaboration Platform</title>
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
                <div class="card">
                    <h2 style="text-align: center;">Forgot Password?</h2>
                    <p style="text-align: center; color: #64748b; margin-bottom: 2rem;">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="email">Email Address:</label>
                            <input type="email" id="email" name="email" class="form-control" required placeholder="your.email@example.com">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                    </form>
                    
                    <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(0,0,0,0.1);">
                        <p style="color: #64748b; margin-bottom: 0.5rem;">Remember your password?</p>
                        <a href="index.php" class="btn btn-secondary btn-block">Back to Login</a>
                    </div>
                </div>
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