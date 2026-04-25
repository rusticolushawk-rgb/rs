<?php
namespace Modules\Studio\Controllers;

use Core\Controllers\BaseController;

class StudioController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM studio_custom_fields ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Studio', 'fas fa-paint-brush', $records);
    }

    public function fields(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM studio_custom_fields ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Studio', 'fas fa-paint-brush', $records);
    }

    public function createField(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('studio_custom_fields', $data);
        $this->audit('create', 'studio', $id, 'Created studio record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/studio');
    }

    public function apiFields(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM studio_custom_fields ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"module_name","label":"Module"},{"key":"field_name","label":"Field Name"},{"key":"field_label","label":"Label"},{"key":"field_type","label":"Type"},{"key":"is_required","label":"Required"}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/studio';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/studio/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/studio']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
