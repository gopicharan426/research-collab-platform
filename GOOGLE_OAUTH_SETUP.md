# Google OAuth Setup Instructions

## Step 1: Create Google OAuth Credentials

1. Go to **Google Cloud Console**: https://console.cloud.google.com/
2. Create a new project or select existing one
3. Go to **APIs & Services** → **Credentials**
4. Click **Create Credentials** → **OAuth client ID**
5. Choose **Web application**
6. Add Authorized redirect URIs:
   - For local: `http://localhost:8080/google_callback.php`
   - For hosting: `http://yoursite.000webhostapp.com/google_callback.php`
7. Copy **Client ID** and **Client Secret**

## Step 2: Update Configuration

Edit `app/config/google_config.php`:

```php
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost:8080/google_callback.php');
```

## Step 3: For Web Hosting

Update redirect URI in `google_config.php`:
```php
define('GOOGLE_REDIRECT_URI', 'http://yoursite.000webhostapp.com/google_callback.php');
```

## Step 4: Test

1. Visit your site
2. Click "Sign in with Google"
3. Authorize the app
4. You'll be logged in automatically

## Features:
- One-click Google login
- Auto-creates user account
- No password needed
- Secure OAuth 2.0 flow

## Files Added:
- `app/config/google_config.php` - Configuration
- `public/google_callback.php` - OAuth handler
- Updated `public/index.php` - Google button