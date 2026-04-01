<?php
require_once '../backend/app/config/database.php';
require_once '../backend/app/config/google_config.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange code for access token
    $ch = curl_init(GOOGLE_TOKEN_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ]));
    $response  = curl_exec($ch);
    curl_close($ch);
    $tokenInfo = json_decode($response, true);

    if (isset($tokenInfo['access_token'])) {
        // Get user info from Google
        $ch = curl_init(GOOGLE_USER_INFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenInfo['access_token']]);
        $userResponse = curl_exec($ch);
        curl_close($ch);
        $userInfo = json_decode($userResponse, true);

        if (isset($userInfo['email'])) {
            $col  = getCollection('users');
            $user = $col->findOne(['email' => $userInfo['email']]);

            if (!$user) {
                // Create new user
                $userId = getNextId('users');
                $col->insertOne([
                    'user_id'    => $userId,
                    'name'       => $userInfo['name'],
                    'username'   => null,
                    'email'      => $userInfo['email'],
                    'password'   => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                    'role'       => null,
                    'department' => null,
                    'is_admin'   => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $userName = $userInfo['name'];
            } else {
                $userId   = (int)$user['user_id'];
                $userName = $user['name'];
            }

            $_SESSION['user_id']       = (int)$userId;
            $_SESSION['user_name']     = $userName;
            $_SESSION['user_username'] = $user['username'] ?? '';
            $_SESSION['user_email']    = $userInfo['email'];
            $_SESSION['is_admin']      = (int)($user['is_admin'] ?? 0);

            header("Location: index.php");
            exit();
        }
    }
}

header("Location: login.php?error=google_login_failed");
exit();
?>
