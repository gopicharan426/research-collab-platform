<?php
require_once '../app/auth/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$message = '';
$messageType = '';

// Handle login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = loginUser($_POST['email'], $_POST['password']);
    if ($result === 'success') {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

// Handle registration
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $result = registerUser($_POST['name'], $_POST['email'], $_POST['password']);
    if ($result === 'success') {
        $message = 'Registration successful! Please login.';
        $messageType = 'success';
    } else {
        $message = $result;
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Collaboration Platform</title>
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

            <div class="dashboard-grid">
                <!-- Login Form -->
                <div class="card">
                    <h2>Login</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        
                        <div class="form-group">
                            <label for="login-email">Email:</label>
                            <input type="email" id="login-email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="login-password">Password:</label>
                            <input type="password" id="login-password" name="password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>

                <!-- Registration Form -->
                <div class="card">
                    <h2>Register</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="register">
                        
                        <div class="form-group">
                            <label for="register-name">Full Name:</label>
                            <input type="text" id="register-name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-email">Email:</label>
                            <input type="email" id="register-email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="register-password">Password:</label>
                            <input type="password" id="register-password" name="password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="card">
                <h2>Welcome to Research Collaboration Platform</h2>
                <p>Connect with fellow researchers, share your ideas, and collaborate on innovative projects. Join our community to:</p>
                <ul style="margin: 1rem 0; padding-left: 2rem;">
                    <li>Share your research ideas and findings</li>
                    <li>Collaborate with other researchers</li>
                    <li>Get feedback on your projects</li>
                    <li>Discover new research opportunities</li>
                </ul>
                <p><strong>Test Account:</strong> Email: test@example.com, Password: password123</p>
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