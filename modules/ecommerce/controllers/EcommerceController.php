<?php
namespace Modules\Ecommerce\Controllers;

use Core\Controllers\BaseController;

class EcommerceController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM ecommerce_orders ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('eCommerce', 'fas fa-shopping-bag', $records);
    }

    public function orders(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM ecommerce_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('eCommerce', 'fas fa-shopping-bag', $records);
    }

    public function showOrder(string $id = ''): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $record = $db->fetch("SELECT * FROM ecommerce_orders WHERE id = ?", [$id]);
        if (!$record) { $this->redirect('/ecommerce'); return; }
        $this->renderModulePage('eCommerce', 'fas fa-shopping-bag', [], $record);
    }

    public function products(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM ecommerce_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('eCommerce', 'fas fa-shopping-bag', $records);
    }

    public function catalog(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM ecommerce_orders ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('eCommerce', 'fas fa-shopping-bag', $records);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"order_number","label":"Order #"},{"key":"customer_name","label":"Customer"},{"key":"status","label":"Status","badge":{"pending":"badge-secondary","processing":"badge-info","shipped":"badge-warning","delivered":"badge-success"}},{"key":"payment_status","label":"Payment","badge":{"pending":"badge-warning","paid":"badge-success","refunded":"badge-danger"}},{"key":"total_amount","label":"Total","currency":true}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/ecommerce';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/ecommerce/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/ecommerce']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
