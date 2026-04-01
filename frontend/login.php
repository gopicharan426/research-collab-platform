<?php
require_once '../backend/app/auth/auth.php';
require_once '../backend/app/config/google_config.php';
require_once '../backend/app/config/recaptcha_config.php';

// Generate Google OAuth URL
$googleAuthUrl = GOOGLE_AUTH_URL . '?' . http_build_query([
    'client_id' => GOOGLE_CLIENT_ID,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'online'
]);

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';
$showForm = isset($_GET['form']) ? $_GET['form'] : 'login';

// Handle login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = loginUser($_POST['email'], $_POST['password']);
    if ($result === 'success') {
        header("Location: index.php");
        exit();
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

// Handle registration with role-based fields and CAPTCHA
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    $department = $_POST['department'] ?? '';
    $class = $_POST['class'] ?? null;
    $designation = $_POST['designation'] ?? null;
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    
    $result = registerUser($name, $username, $email, $password, $role, $department, $class, $designation, $recaptchaResponse);
    
    if ($result === 'success') {
        $message = 'Registration successful! Please login.';
        $messageType = 'success';
        $showForm = 'login';
    } else {
        $message = $result;
        $messageType = 'error';
    }
}

// Handle forgot password
if (isset($_POST['action']) && $_POST['action'] === 'forgot_password') {
    header("Location: reset_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Research Collaboration Platform</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
                <?php if ($showForm === 'login'): ?>
                    <!-- Login Form -->
                    <div class="card">
                        <h2 style="text-align: center;">Login to Your Account</h2>
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
                            
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                        
                        <div style="text-align: center; margin: 1.5rem 0;">
                            <a href="reset_password.php" style="color: var(--primary); font-size: 14px;">Forgot Password?</a>
                        </div>
                        
                        <div style="text-align: center; margin: 1.5rem 0;">
                            <p style="color: #6c757d; margin-bottom: 1rem;">OR</p>
                        </div>
                        
                        <a href="<?php echo htmlspecialchars($googleAuthUrl); ?>" class="btn btn-primary btn-block" style="background: #4285f4; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                                <path d="M9.003 18c2.43 0 4.467-.806 5.956-2.18L12.05 13.56c-.806.54-1.836.86-3.047.86-2.344 0-4.328-1.584-5.036-3.711H.96v2.332C2.44 15.983 5.485 18 9.003 18z" fill="#34A853"/>
                                <path d="M3.964 10.712c-.18-.54-.282-1.117-.282-1.71 0-.593.102-1.17.282-1.71V4.96H.957C.347 6.175 0 7.55 0 9.002c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                                <path d="M9.003 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.464.891 11.426 0 9.003 0 5.485 0 2.44 2.017.96 4.958L3.967 7.29c.708-2.127 2.692-3.71 5.036-3.71z" fill="#EA4335"/>
                            </svg>
                            Sign in with Google
                        </a>
                        
                        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <p style="color: #6c757d; margin-bottom: 0.5rem;">Don't have an account?</p>
                            <a href="?form=register" class="btn btn-secondary btn-block">Create New Account</a>
                        </div>
                    </div>
                
                <?php elseif ($showForm === 'register'): ?>
                    <!-- Enhanced Registration Form with Role-Based Fields and CAPTCHA -->
                    <div class="card">
                        <h2 style="text-align: center;">Create New Account</h2>
                        <form method="POST" id="registerForm">
                            <input type="hidden" name="action" value="register">
                            
                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="register-name">Full Name: <span style="color: red;">*</span></label>
                                <input type="text" id="register-name" name="name" class="form-control" required>
                            </div>
                            
                            <!-- Username -->
                            <div class="form-group">
                                <label for="register-username">Username: <span style="color: red;">*</span></label>
                                <input type="text" id="register-username" name="username" class="form-control" required pattern="[a-zA-Z0-9_]{3,30}" placeholder="e.g., john_doe123">
                                <small style="color: #6c757d;">3-30 characters, letters, numbers, underscores only</small>
                            </div>
                            
                            <!-- Email -->
                            <div class="form-group">
                                <label for="register-email">Email: <span style="color: red;">*</span></label>
                                <input type="email" id="register-email" name="email" class="form-control" required>
                            </div>
                            
                            <!-- Password -->
                            <div class="form-group">
                                <label for="register-password">Password: <span style="color: red;">*</span></label>
                                <input type="password" id="register-password" name="password" class="form-control" required minlength="6">
                                <small style="color: #6c757d;">Minimum 6 characters</small>
                            </div>
                            
                            <!-- Role Selection (Student/Professor) -->
                            <div class="form-group">
                                <label for="register-role">I am a: <span style="color: red;">*</span></label>
                                <select id="register-role" name="role" class="form-control" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="student">Student</option>
                                    <option value="professor">Professor</option>
                                </select>
                            </div>
                            
                            <!-- Department (Common for both) -->
                            <div class="form-group">
                                <label for="register-department">Department: <span style="color: red;">*</span></label>
                                <input type="text" id="register-department" name="department" class="form-control" required placeholder="e.g., Computer Science">
                            </div>
                            
                            <!-- Class (Only for Students) -->
                            <div class="form-group" id="class-field" style="display: none;">
                                <label for="register-class">Class: <span style="color: red;">*</span></label>
                                <input type="text" id="register-class" name="class" class="form-control" placeholder="e.g., BSc CS 3rd Year">
                            </div>
                            
                            <!-- Designation (Only for Professors) -->
                            <div class="form-group" id="designation-field" style="display: none;">
                                <label for="register-designation">Designation: <span style="color: red;">*</span></label>
                                <input type="text" id="register-designation" name="designation" class="form-control" placeholder="e.g., Assistant Professor">
                            </div>
                            
                            <!-- Google reCAPTCHA -->
                            <div class="form-group" style="margin: 1.5rem 0;">
                                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </form>
                        
                        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <p style="color: #6c757d; margin-bottom: 0.5rem;">Already have an account?</p>
                            <a href="?form=login" class="btn btn-secondary btn-block">Back to Login</a>
                        </div>
                    </div>
                
                <?php elseif ($showForm === 'forgot'): ?>
                    <!-- Forgot Password Form -->
                    <div class="card">
                        <h2 style="text-align: center;">Reset Password</h2>
                        <p style="text-align: center; color: #6c757d; margin-bottom: 1.5rem;">Click below to reset your password</p>
                        
                        <a href="reset_password.php" class="btn btn-primary btn-block">Go to Password Reset</a>
                        
                        <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <a href="?form=login" class="btn btn-secondary btn-block">Back to Login</a>
                        </div>
                    </div>
                <?php endif; ?>
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
    
    <!-- Role-Based Dynamic Fields Script -->
    <script>
        // Get form elements
        const roleSelect = document.getElementById('register-role');
        const classField = document.getElementById('class-field');
        const designationField = document.getElementById('designation-field');
        const classInput = document.getElementById('register-class');
        const designationInput = document.getElementById('register-designation');
        
        // Listen for role selection changes
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                const selectedRole = this.value;
                
                // Hide both fields initially
                classField.style.display = 'none';
                designationField.style.display = 'none';
                
                // Remove required attribute from both
                classInput.removeAttribute('required');
                designationInput.removeAttribute('required');
                
                // Clear values
                classInput.value = '';
                designationInput.value = '';
                
                // Show appropriate field based on role
                if (selectedRole === 'student') {
                    classField.style.display = 'block';
                    classInput.setAttribute('required', 'required');
                } else if (selectedRole === 'professor') {
                    designationField.style.display = 'block';
                    designationInput.setAttribute('required', 'required');
                }
            });
        }
        
        // Form validation before submit
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                const role = roleSelect.value;
                
                // Validate role-specific fields
                if (role === 'student' && !classInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter your class.');
                    classInput.focus();
                    return false;
                }
                
                if (role === 'professor' && !designationInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter your designation.');
                    designationInput.focus();
                    return false;
                }
                
                // Validate reCAPTCHA
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    e.preventDefault();
                    alert('Please complete the CAPTCHA verification.');
                    return false;
                }
            });
        }
    </script>
</body>
</html>