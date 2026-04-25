<?php
namespace Modules\Hr\Controllers;

use Core\Controllers\BaseController;

class HrController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM hr_employees ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function employees(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_employees ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function createEmployee(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_employees ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function storeEmployee(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('hr_employees', $data);
        $this->audit('create', 'hr', $id, 'Created hr record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/hr');
    }

    public function showEmployee(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM hr_employees WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/hr'); return; }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', [], $record);
    }

    public function departments(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_departments ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function attendance(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_attendance ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function payroll(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_payroll ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function leaves(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM hr_leaves ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Human Resources', 'fas fa-user-tie', $records);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM hr_employees ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"employee_number","label":"Employee #"},{"key":"name","label":"Name"},{"key":"department_name","label":"Department"},{"key":"position","label":"Position"},{"key":"hire_date","label":"Hire Date"},{"key":"salary","label":"Salary","currency":true}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/hr';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/hr/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/hr']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
