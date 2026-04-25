<?php
namespace Modules\Fleet\Controllers;

use Core\Controllers\BaseController;

class FleetController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM fleet_vehicles ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Fleet', 'fas fa-truck', $records);
    }

    public function vehicles(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM fleet_vehicles ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Fleet', 'fas fa-truck', $records);
    }

    public function createVehicle(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM fleet_vehicles ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Fleet', 'fas fa-truck', $records);
    }

    public function storeVehicle(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('fleet_vehicles', $data);
        $this->audit('create', 'fleet', $id, 'Created fleet record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/fleet');
    }

    public function showVehicle(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM fleet_vehicles WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/fleet'); return; }
        $this->renderModulePage('Fleet', 'fas fa-truck', [], $record);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"name","label":"Vehicle"},{"key":"license_plate","label":"License Plate"},{"key":"make","label":"Make"},{"key":"model","label":"Model"},{"key":"fuel_type","label":"Fuel"},{"key":"mileage","label":"Mileage"},{"key":"status","label":"Status","badge":{"available":"badge-success","in_use":"badge-info","maintenance":"badge-warning","retired":"badge-secondary"}}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/fleet';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/fleet/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/fleet']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
