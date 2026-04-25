<?php
namespace Modules\Prepress\Controllers;

use Core\Controllers\BaseController;

class PrepressController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM prepress_jobs ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Prepress Workflow', 'fas fa-print', $records);
    }

    public function jobs(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM prepress_jobs ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Prepress Workflow', 'fas fa-print', $records);
    }

    public function createJob(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM prepress_jobs ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Prepress Workflow', 'fas fa-print', $records);
    }

    public function storeJob(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('prepress_jobs', $data);
        $this->audit('create', 'prepress', $id, 'Created prepress record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/prepress');
    }

    public function showJob(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM prepress_jobs WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/prepress'); return; }
        $this->renderModulePage('Prepress Workflow', 'fas fa-print', [], $record);
    }

    public function uploadFile(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        // Handle file upload
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            $filename = time() . '_' . $file['name'];
            $uploadDir = BASE_PATH . '/storage/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
        }
        if ($this->isAjax()) { $this->json(['success' => true]); }
        $this->redirect('/prepress');
    }

    public function proofing(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM prepress_jobs ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Prepress Workflow', 'fas fa-print', $records);
    }

    public function approveProof(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $db->update('prepress_jobs', ['status' => 'approved', 'approved_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
        if ($this->isAjax()) { $this->json(['success' => true]); }
        $this->redirect('/prepress');
    }

    public function rejectProof(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $db->update('prepress_jobs', ['status' => 'rejected', 'rejected_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
        if ($this->isAjax()) { $this->json(['success' => true]); }
        $this->redirect('/prepress');
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM prepress_jobs ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"job_number","label":"Job #"},{"key":"job_name","label":"Job Name"},{"key":"client_name","label":"Client"},{"key":"color_mode","label":"Color Mode"},{"key":"status","label":"Status","badge":{"new":"badge-info","in_progress":"badge-warning","proofing":"badge-primary","approved":"badge-success","printing":"badge-info","completed":"badge-secondary"}},{"key":"priority","label":"Priority","badge":{"low":"badge-secondary","medium":"badge-warning","high":"badge-danger"}},{"key":"due_date","label":"Due Date"}]', true);
        $kanbanColumns = json_decode('{"new":{"label":"New","color":"#17A2B8"},"in_progress":{"label":"In Progress","color":"#F0AD4E"},"proofing":{"label":"Proofing","color":"#875A7B"},"approved":{"label":"Approved","color":"#28A745"},"printing":{"label":"Printing","color":"#2980B9"},"completed":{"label":"Completed","color":"#6C757D"}}', true);
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/prepress';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/prepress/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/prepress']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
