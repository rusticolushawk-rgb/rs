<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class AuditLogApiController extends BaseController
{
    public function list(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $page = max(1, (int)$this->input('page', 1));
        $perPage = (int)$this->input('per_page', 50);
        $offset = ($page - 1) * $perPage;

        $total = $db->count('audit_logs');
        $logs = $db->fetchAll(
            "SELECT al.*, u.name as user_name 
             FROM audit_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}"
        );

        $this->json([
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }
}
