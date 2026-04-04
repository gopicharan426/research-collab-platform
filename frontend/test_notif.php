<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'database.php';
require_once 'auth.php';
require_once 'notifications_fn.php';
?>
<!DOCTYPE html>
<html>
<head><title>Notif Test</title></head>
<body>
<h3>Notification Fetch Test</h3>
<div id="result">Testing...</div>
<div id="badge_result"></div>

<script>
// Test 1: fetch notifications.php?action=get
fetch('notifications.php?action=get')
    .then(r => r.text())
    .then(text => {
        document.getElementById('result').innerHTML = '<b>Raw response:</b><br><pre>' + text + '</pre>';
        try {
            const data = JSON.parse(text);
            document.getElementById('badge_result').innerHTML = 
                '<b>Parsed:</b> unread=' + data.unread + ', notifications=' + (data.notifications ? data.notifications.length : 0);
        } catch(e) {
            document.getElementById('badge_result').innerHTML = '<b style="color:red">JSON parse error: ' + e.message + '</b>';
        }
    })
    .catch(e => {
        document.getElementById('result').innerHTML = '<b style="color:red">Fetch error: ' + e.message + '</b>';
    });
</script>
</body>
</html>
