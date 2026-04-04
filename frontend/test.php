<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: Loading database.php...<br>";
require_once __DIR__ . '/database.php';
echo "✅ database.php OK<br>";

echo "Step 2: Loading recaptcha_config.php...<br>";
require_once __DIR__ . '/recaptcha_config.php';
echo "✅ recaptcha_config.php OK<br>";

echo "Step 3: Loading auth.php...<br>";
require_once __DIR__ . '/auth.php';
echo "✅ auth.php OK<br>";

echo "Step 4: Loading google_config.php...<br>";
require_once __DIR__ . '/google_config.php';
echo "✅ google_config.php OK<br>";

echo "Step 5: Testing DB connection...<br>";
$pdo = getDBConnection();
echo "✅ getDBConnection() OK<br>";

echo "<br><strong style='color:green'>All files loaded successfully!</strong>";
?>
