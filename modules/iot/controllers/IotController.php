<?php
namespace Modules\Iot\Controllers;

use Core\Controllers\BaseController;

class IotController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM iot_devices ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('IoT', 'fas fa-microchip', $records);
    }

    public function devices(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM iot_devices ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('IoT', 'fas fa-microchip', $records);
    }

    public function createDevice(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM iot_devices ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('IoT', 'fas fa-microchip', $records);
    }

    public function storeDevice(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('iot_devices', $data);
        $this->audit('create', 'iot', $id, 'Created iot record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/iot');
    }

    public function showDevice(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM iot_devices WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/iot'); return; }
        $this->renderModulePage('IoT', 'fas fa-microchip', [], $record);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"name","label":"Device Name"},{"key":"device_type","label":"Type"},{"key":"location","label":"Location"},{"key":"status","label":"Status","badge":{"online":"badge-success","offline":"badge-danger","error":"badge-warning"}},{"key":"last_reading","label":"Last Reading"}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/iot';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/iot/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/iot']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
