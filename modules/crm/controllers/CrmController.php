<?php
namespace Modules\Crm\Controllers;

use Core\Controllers\BaseController;

class CrmController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function leads(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function createLead(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function storeLead(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('crm_leads', $data);
        $this->audit('create', 'crm', $id, 'Created crm record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/crm');
    }

    public function showLead(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM crm_leads WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/crm'); return; }
        $this->renderModulePage('CRM', 'fas fa-handshake', [], $record);
    }

    public function updateLead(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $db->update('crm_leads', $data, 'id = ?', [$id]);
        $this->audit('update', 'crm', (int)$id, 'Updated crm record');
        if ($this->isAjax()) { $this->json(['success' => true]); }
        $this->redirect('/crm');
    }

    public function opportunities(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM crm_opportunities ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function contacts(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM crm_contacts ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function createContact(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('CRM', 'fas fa-handshake', $records);
    }

    public function storeContact(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('crm_leads', $data);
        $this->audit('create', 'crm', $id, 'Created crm record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/crm');
    }

    public function apiLeads(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM crm_leads ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"name","label":"Name"},{"key":"contact_name","label":"Contact"},{"key":"company_name","label":"Company"},{"key":"stage","label":"Stage","badge":{"new":"badge-info","qualified":"badge-warning","proposition":"badge-primary","won":"badge-success","lost":"badge-danger"}},{"key":"expected_revenue","label":"Revenue","currency":true},{"key":"probability","label":"Probability"}]', true);
        $kanbanColumns = json_decode('{"new":{"label":"New","color":"#17A2B8"},"qualified":{"label":"Qualified","color":"#F0AD4E"},"proposition":{"label":"Proposition","color":"#875A7B"},"won":{"label":"Won","color":"#28A745"},"lost":{"label":"Lost","color":"#DC3545"}}', true);
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/crm';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'stage';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/crm/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/crm']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
