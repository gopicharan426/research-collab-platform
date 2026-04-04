<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Step 1: Loading database.php...<br>";
require_once 'database.php';
echo "✅ database.php OK<br>";

echo "Step 2: Loading recaptcha_config.php...<br>";
require_once 'recaptcha_config.php';
echo "✅ recaptcha_config.php OK<br>";

echo "Step 3: Loading auth.php...<br>";
require_once 'auth.php';
echo "✅ auth.php OK<br>";

echo "Step 4: Loading google_config.php...<br>";
require_once 'google_config.php';
echo "✅ google_config.php OK<br>";

echo "Step 5: Loading posts.php...<br>";
require_once 'posts.php';
echo "✅ posts.php OK<br>";

echo "Step 6: Loading comments.php...<br>";
require_once 'comments.php';
echo "✅ comments.php OK<br>";

echo "Step 7: Loading likes.php...<br>";
require_once 'likes.php';
echo "✅ likes.php OK<br>";

echo "Step 8: Loading notifications_fn.php...<br>";
require_once 'notifications_fn.php';
echo "✅ notifications_fn.php OK<br>";

echo "<br>✅ All files loaded successfully!";
?>
