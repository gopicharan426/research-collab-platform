<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/google_config.php';

if (isset($_GET['code'])) {
    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code' => $_GET['code'], 'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET, 'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ]));
    $tokenInfo = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (isset($tokenInfo['access_token'])) {
        $ch = curl_init(GOOGLE_USER_INFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenInfo['access_token']]);
        $userInfo = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (isset($userInfo['email'])) {
            $pdo  = getDBConnection();
            $stmt = $pdo->prepare("SELECT user_id, name, username, is_admin FROM users WHERE email = ?");
            $stmt->execute([$userInfo['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$userInfo['name'], $userInfo['email'], password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT)]);
                $userId   = $pdo->lastInsertId();
                $userName = $userInfo['name'];
                $isAdmin  = 0;
                $username = '';
            } else {
                $userId   = $user['user_id'];
                $userName = $user['name'];
                $isAdmin  = $user['is_admin'] ?? 0;
                $username = $user['username'] ?? '';
            }

            $_SESSION['user_id']       = $userId;
            $_SESSION['user_name']     = $userName;
            $_SESSION['user_username'] = $username;
            $_SESSION['user_email']    = $userInfo['email'];
            $_SESSION['is_admin']      = $isAdmin;

            header("Location: index.php");
            exit();
        }
    }
}

header("Location: login.php?error=google_login_failed");
exit();
?>
