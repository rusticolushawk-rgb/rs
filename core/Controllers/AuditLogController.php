<?php
namespace Core\Controllers;

class AuditLogController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $page = max(1, (int)$this->input('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $total = $db->count('audit_logs');
        $logs = $db->fetchAll(
            "SELECT al.*, u.name as user_name 
             FROM audit_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT {$perPage} OFFSET {$offset}"
        );

        $this->view('audit.index', [
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'pageTitle' => t('audit_logs'),
            'breadcrumb' => [['label' => t('audit_logs'), 'url' => '/audit-logs']],
        ]);
    }
}
