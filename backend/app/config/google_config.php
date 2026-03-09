<?php
// Google OAuth Configuration
// Get credentials from: https://console.cloud.google.com/

define('GOOGLE_CLIENT_ID', '726256010284-tldt5e4fgkfhmg69r0lat5gnjfsl3tn0.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'http://localhost:8080/google_callback.php'); // Change for hosting

// Google OAuth URLs
define('GOOGLE_AUTH_URL', 'https://accounts.google.com/o/oauth2/v2/auth');
define('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token');
define('GOOGLE_USER_INFO_URL', 'https://www.googleapis.com/oauth2/v2/userinfo');
?>