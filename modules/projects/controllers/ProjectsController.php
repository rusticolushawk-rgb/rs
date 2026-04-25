<?php
namespace Modules\Projects\Controllers;

use Core\Controllers\BaseController;

class ProjectsController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM project_projects ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Projects', 'fas fa-project-diagram', $records);
    }

    public function createProject(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM project_projects ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Projects', 'fas fa-project-diagram', $records);
    }

    public function storeProject(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('project_projects', $data);
        $this->audit('create', 'projects', $id, 'Created projects record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/projects');
    }

    public function showProject(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM project_projects WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/projects'); return; }
        $this->renderModulePage('Projects', 'fas fa-project-diagram', [], $record);
    }

    public function tasks(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM project_tasks ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Projects', 'fas fa-project-diagram', $records);
    }

    public function timesheets(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM project_timesheets ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Projects', 'fas fa-project-diagram', $records);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM project_projects ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"name","label":"Project Name"},{"key":"status","label":"Status","badge":{"planning":"badge-secondary","in_progress":"badge-info","completed":"badge-success","on_hold":"badge-warning"}},{"key":"start_date","label":"Start Date"},{"key":"end_date","label":"End Date"},{"key":"progress","label":"Progress"},{"key":"budget","label":"Budget","currency":true}]', true);
        $kanbanColumns = json_decode('{"todo":{"label":"To Do","color":"#6C757D"},"in_progress":{"label":"In Progress","color":"#17A2B8"},"in_review":{"label":"In Review","color":"#F0AD4E"},"done":{"label":"Done","color":"#28A745"},"blocked":{"label":"Blocked","color":"#DC3545"}}', true);
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/projects';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/projects/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/projects']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
