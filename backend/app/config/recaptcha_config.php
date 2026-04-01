<?php
// Google reCAPTCHA Configuration
// Get your keys from: https://www.google.com/recaptcha/admin

// reCAPTCHA Site Key (Public Key) - Use in HTML forms
define('RECAPTCHA_SITE_KEY', '6LestocsAAAAAOxQBgt00sfXRH5O65R5Rmb4K8Ei');

// reCAPTCHA Secret Key (Private Key) - Use in PHP verification
define('RECAPTCHA_SECRET_KEY', '6LestocsAAAAAKF67zGlW2dC8jPZYQ2D0c73Q2YU');

// reCAPTCHA Verification URL
define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');
?>
