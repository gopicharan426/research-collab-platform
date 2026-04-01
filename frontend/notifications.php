<?php
require_once '../backend/app/auth/auth.php';
require_once '../backend/app/social/notifications.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'unauthenticated']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'get':
        $notifications = getNotifications($_SESSION['user_id']);
        $unread = getUnreadCount($_SESSION['user_id']);
        echo json_encode(['notifications' => $notifications, 'unread' => $unread]);
        break;

    case 'unread_count':
        echo json_encode(['count' => getUnreadCount($_SESSION['user_id'])]);
        break;

    case 'mark_one_read':
        $notifId = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
        if ($notifId > 0) {
            markAsRead($notifId, $_SESSION['user_id']);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'invalid id']);
        }
        break;

    case 'mark_all_read':
        markAllAsRead($_SESSION['user_id']);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'invalid action']);
}
?>
