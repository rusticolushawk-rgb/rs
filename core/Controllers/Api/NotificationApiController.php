<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class NotificationApiController extends BaseController
{
    public function list(): void
    {
        $this->requireAuth();
        $userId = $this->app->getAuth()->id();
        $db = $this->app->getDb();

        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 20",
            [$userId]
        );
        $unreadCount = $db->count('notifications', 'user_id = ? AND is_read = 0', [$userId]);

        $this->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(): void
    {
        $this->requireAuth();
        $userId = $this->app->getAuth()->id();
        $notificationId = $this->input('id');

        if ($notificationId) {
            $this->app->getDb()->update('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$notificationId, $userId]);
        } else {
            $this->app->getDb()->update('notifications', ['is_read' => 1], 'user_id = ?', [$userId]);
        }

        $this->json(['success' => true]);
    }
}
