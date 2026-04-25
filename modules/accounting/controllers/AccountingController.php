<?php
namespace Modules\Accounting\Controllers;

use Core\Controllers\BaseController;

class AccountingController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM accounting_entries ORDER BY id DESC LIMIT 100");
        $this->renderModulePage('Accounting', 'fas fa-calculator', $records);
    }

    public function invoices(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM accounting_entries ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Accounting', 'fas fa-calculator', $records);
    }

    public function payments(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM accounting_payments ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Accounting', 'fas fa-calculator', $records);
    }

    public function journals(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM accounting_journals ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Accounting', 'fas fa-calculator', $records);
    }

    public function accounts(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        try {
            $records = $db->fetchAll("SELECT * FROM accounting_accounts ORDER BY id DESC LIMIT 100");
        } catch (\Exception $e) {
            $records = [];
        }
        $this->renderModulePage('Accounting', 'fas fa-calculator', $records);
    }

    public function apiStats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();
        $records = $db->fetchAll("SELECT * FROM accounting_entries ORDER BY id DESC LIMIT 100");
        $this->json(['data' => $records, 'total' => count($records)]);
    }

    private function renderModulePage(string $title, string $icon, array $records = [], ?array $record = null): void
    {
        $columns = json_decode('[{"key":"entry_number","label":"Entry #"},{"key":"entry_date","label":"Date"},{"key":"reference","label":"Reference"},{"key":"status","label":"Status","badge":{"draft":"badge-secondary","posted":"badge-success","cancelled":"badge-danger"}},{"key":"total_debit","label":"Debit","currency":true},{"key":"total_credit","label":"Credit","currency":true}]', true);
        $kanbanColumns = null;
        $moduleTitle = $title;
        $moduleIcon = $icon;
        $baseUrl = '/accounting';
        $kanbanTitleField = 'name';
        $kanbanStatusField = 'status';
        $kanbanMetaFields = ['created_at'];
        $createUrl = '/accounting/create';
        $smartButtons = [];

        $pageTitle = $title;
        $breadcrumb = [['label' => $title, 'url' => '/accounting']];

        $this->view('components.module_page', compact(
            'columns', 'records', 'kanbanColumns', 'moduleTitle', 'moduleIcon',
            'baseUrl', 'kanbanTitleField', 'kanbanStatusField', 'kanbanMetaFields',
            'createUrl', 'smartButtons', 'pageTitle', 'breadcrumb'
        ));
    }
}
