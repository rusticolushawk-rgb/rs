<?php
namespace Core\Controllers;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $db = $this->app->getDb();

        // Get dashboard stats
        $stats = [
            'total_modules' => $db->count('modules', 'is_active = 1'),
            'total_users' => $db->count('users', 'is_active = 1'),
            'total_companies' => $db->count('companies', 'is_active = 1'),
        ];

        // Recent audit logs
        try {
            $recentActivity = $db->fetchAll(
                "SELECT al.*, u.name as user_name 
                 FROM audit_logs al 
                 LEFT JOIN users u ON al.user_id = u.id 
                 ORDER BY al.created_at DESC LIMIT 10"
            );
        } catch (\Exception $e) {
            $recentActivity = [];
        }

        // Recent notifications
        try {
            $notifications = $db->fetchAll(
                "SELECT * FROM notifications 
                 WHERE user_id = ? 
                 ORDER BY created_at DESC LIMIT 5",
                [$this->app->getAuth()->id() ?? 0]
            );
        } catch (\Exception $e) {
            $notifications = [];
        }

        $this->view('dashboard.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'notifications' => $notifications,
            'pageTitle' => t('dashboard'),
        ]);
    }
}
