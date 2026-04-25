<?php
namespace Modules\Maintenance\Controllers;

use Core\Controllers\BaseController;

class MaintenanceController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM maintenance_requests ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Maintenance', 'fas fa-wrench', $records);
    }

    public function requests(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM maintenance_requests ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Maintenance', 'fas fa-wrench', $records);
    }

    public function createRequest(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM maintenance_requests ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Maintenance', 'fas fa-wrench', $records);
    }

    public function storeRequest(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('maintenance_requests', $data);
        $this->audit('create', 'maintenance', $id, 'Created maintenance record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/maintenance');
    }

    public function equipment(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM maintenance_equipment ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Maintenance', 'fas fa-wrench', $records);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"description","label":"Description"},{"key":"request_type","label":"Type","badge":{"preventive":"badge-info","corrective":"badge-warning"}},{"key":"priority","label":"Priority","badge":{"low":"badge-secondary","medium":"badge-warning","high":"badge-danger"}},{"key":"status","label":"Status","badge":{"pending":"badge-secondary","scheduled":"badge-info","in_progress":"badge-warning","completed":"badge-success"}},{"key":"scheduled_date","label":"Scheduled Date"}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/maintenance';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/maintenance/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/maintenance']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
