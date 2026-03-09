<?php
require_once '../app/config/database.php';
require_once '../app/config/google_config.php';

// Handle OAuth callback
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Exchange code for access token
    $tokenData = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    $response = curl_exec($ch);
    curl_close($ch);
    
    $tokenInfo = json_decode($response, true);
    
    if (isset($tokenInfo['access_token'])) {
        // Get user info
        $ch = curl_init(GOOGLE_USER_INFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $tokenInfo['access_token']
        ]);
        $userResponse = curl_exec($ch);
        curl_close($ch);
        
        $userInfo = json_decode($userResponse, true);
        
        if (isset($userInfo['email'])) {
            $pdo = getDBConnection();
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = ?");
            $stmt->execute([$userInfo['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                // Create new user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([
                    $userInfo['name'],
                    $userInfo['email'],
                    password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)
                ]);
                $userId = $pdo->lastInsertId();
                $userName = $userInfo['name'];
            } else {
                $userId = $user['user_id'];
                $userName = $user['name'];
            }
            
            // Set session
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $userName;
            $_SESSION['user_email'] = $userInfo['email'];
            
            header("Location: index.php");
            exit();
        }
    }
}

header("Location: index.php?error=google_login_failed");
exit();
?>