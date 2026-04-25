<?php
namespace Modules\Purchase\Controllers;

use Core\Controllers\BaseController;

class PurchaseController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function orders(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function createOrder(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function storeOrder(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('purchase_orders', $data);
        $this->audit('create', 'purchase', $id, 'Created purchase record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/purchase');
    }

    public function showOrder(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM purchase_orders WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/purchase'); return; }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', [], $record);
    }

    public function vendors(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM purchase_vendors ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function createVendor(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function storeVendor(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('purchase_orders', $data);
        $this->audit('create', 'purchase', $id, 'Created purchase record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/purchase');
    }

    public function rfq(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Purchase', 'fas fa-shopping-cart', $records);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM purchase_orders ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"po_number","label":"PO #"},{"key":"vendor_name","label":"Vendor"},{"key":"order_date","label":"Date"},{"key":"status","label":"Status","badge":{"draft":"badge-secondary","confirmed":"badge-info","received":"badge-success","cancelled":"badge-danger"}},{"key":"total_amount","label":"Total","currency":true}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/purchase';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/purchase/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/purchase']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
