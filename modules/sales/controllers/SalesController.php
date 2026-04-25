<?php
namespace Modules\Sales\Controllers;

use Core\Controllers\BaseController;

class SalesController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function orders(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function createOrder(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function storeOrder(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('sales_orders', $data);
        $this->audit('create', 'sales', $id, 'Created sales record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/sales');
    }

    public function showOrder(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM sales_orders WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/sales'); return; }
        $this->renderModulePage('Sales', 'fas fa-chart-line', [], $record);
    }

    public function quotations(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function invoices(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_invoices ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function customers(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_customers ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function createCustomer(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function storeCustomer(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('sales_orders', $data);
        $this->audit('create', 'sales', $id, 'Created sales record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/sales');
    }

    public function products(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_products ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function createProduct(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Sales', 'fas fa-chart-line', $records);
    }

    public function storeProduct(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $data = $this->allInput();
        unset($data['_csrf_token']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = $db->insert('sales_orders', $data);
        $this->audit('create', 'sales', $id, 'Created sales record');
        if ($this->isAjax()) { $this->json(['success' => true, 'id' => $id]); }
        $this->redirect('/sales');
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM sales_orders ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"order_number","label":"Order #"},{"key":"customer_name","label":"Customer"},{"key":"order_date","label":"Date"},{"key":"status","label":"Status","badge":{"draft":"badge-secondary","confirmed":"badge-info","shipped":"badge-warning","delivered":"badge-success","cancelled":"badge-danger"}},{"key":"payment_status","label":"Payment","badge":{"paid":"badge-success","unpaid":"badge-danger","partial":"badge-warning"}},{"key":"total_amount","label":"Total","currency":true}]', true);
        $kanbanColumns = json_decode('{"draft":{"label":"Draft","color":"#6C757D"},"confirmed":{"label":"Confirmed","color":"#17A2B8"},"shipped":{"label":"Shipped","color":"#F0AD4E"},"delivered":{"label":"Delivered","color":"#28A745"},"cancelled":{"label":"Cancelled","color":"#DC3545"}}', true);
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/sales';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/sales/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/sales']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
