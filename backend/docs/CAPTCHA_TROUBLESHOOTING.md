# CAPTCHA Troubleshooting Guide

## Issue: "CAPTCHA verification failed"

### Possible Causes:

1. **Invalid Keys** - Keys might be for wrong domain
2. **Domain Mismatch** - Keys registered for different domain
3. **Network Issue** - Can't reach Google API
4. **cURL Not Enabled** - PHP cURL extension missing

---

## Quick Fix Options:

### Option 1: Temporarily Disable CAPTCHA (For Testing)

Edit `backend/app/auth/auth.php` and change line 11 to:

```php
function verifyRecaptcha($recaptchaResponse) {
    return true; // Temporarily bypass CAPTCHA
}
```

This allows you to test the role-based registration without CAPTCHA.

---

### Option 2: Check Your Keys

Your current keys:
- Site Key: `6LestocsAAAAAOxQBgt00sfXRH5O65R5Rmb4K8Ei`
- Secret Key: `6LestocsAAAAAKF67zGlW2dC8jPZYQ2D0c73Q2YU`

Verify at: https://www.google.com/recaptcha/admin

Make sure:
- ✅ Domain includes `localhost`
- ✅ reCAPTCHA type is v2 Checkbox
- ✅ Keys are active

---

### Option 3: Check PHP Error Logs

Location: `C:\xampp\php\logs\php_error_log`

Look for lines starting with "reCAPTCHA" to see the actual error.

---

### Option 4: Test CAPTCHA Manually

Create `test_captcha.php` in frontend folder:

```php
<?php
require_once '../backend/app/config/recaptcha_config.php';

if ($_POST) {
    $response = $_POST['g-recaptcha-response'] ?? '';
    
    $postData = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $response
    ];
    
    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    echo "<pre>";
    echo "Response: " . $result;
    echo "</pre>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>
    <h1>Test reCAPTCHA</h1>
    <form method="POST">
        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
        <button type="submit">Test</button>
    </form>
</body>
</html>
```

Visit: http://localhost:8080/test_captcha.php

---

### Option 5: Check cURL Extension

Create `check_curl.php`:

```php
<?php
if (function_exists('curl_version')) {
    echo "cURL is enabled<br>";
    print_r(curl_version());
} else {
    echo "cURL is NOT enabled";
}
?>
```

If cURL is disabled, enable it in `php.ini`:
- Uncomment: `extension=curl`
- Restart Apache

---

## Recommended Solution:

**For now, temporarily disable CAPTCHA to test role-based registration:**

In `backend/app/auth/auth.php`, change:

```php
function verifyRecaptcha($recaptchaResponse) {
    return true; // Skip verification for testing
}
```

Then test the registration form. Once role-based fields work, we can fix CAPTCHA separately.

---

## Common Error Codes:

- `missing-input-secret` - Secret key is missing
- `invalid-input-secret` - Secret key is invalid
- `missing-input-response` - User didn't complete CAPTCHA
- `invalid-input-response` - CAPTCHA response is invalid
- `timeout-or-duplicate` - CAPTCHA expired or already used
