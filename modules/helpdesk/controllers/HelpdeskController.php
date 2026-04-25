<?php
namespace Modules\Helpdesk\Controllers;

use Core\Controllers\BaseController;

class HelpdeskController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM helpdesk_tickets ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Helpdesk', 'fas fa-headset', $records);
    }

    public function tickets(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM helpdesk_tickets ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Helpdesk', 'fas fa-headset', $records);
    }

    public function createTicket(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM helpdesk_tickets ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Helpdesk', 'fas fa-headset', $records);
    }

    public function storeTicket(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('helpdesk_tickets', $data);
        $this->audit('create', 'helpdesk', $id, 'Created helpdesk record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/helpdesk');
    }

    public function showTicket(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM helpdesk_tickets WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/helpdesk'); return; }
        $this->renderModulePage('Helpdesk', 'fas fa-headset', [], $record);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM helpdesk_tickets ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"ticket_number","label":"Ticket #"},{"key":"subject","label":"Subject"},{"key":"customer_name","label":"Customer"},{"key":"priority","label":"Priority","badge":{"low":"badge-secondary","medium":"badge-warning","high":"badge-danger","urgent":"badge-danger"}},{"key":"status","label":"Status","badge":{"new":"badge-info","in_progress":"badge-warning","resolved":"badge-success","closed":"badge-secondary"}}]', true);
        $kanbanColumns = json_decode('{"new":{"label":"New","color":"#17A2B8"},"in_progress":{"label":"In Progress","color":"#F0AD4E"},"resolved":{"label":"Resolved","color":"#28A745"},"closed":{"label":"Closed","color":"#6C757D"}}', true);
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/helpdesk';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/helpdesk/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/helpdesk']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
