<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class SidebarApiController extends BaseController
{
    public function getMenu(): void
    {
        $this->requireAuth();

        $menu = [
            ['label' => t('dashboard'), 'icon' => 'fas fa-tachometer-alt', 'url' => '/dashboard', 'id' => 'dashboard'],
        ];

        // Add active module menus
        $activeModules = $this->app->getModuleLoader()->getActiveModules();
        foreach ($activeModules as $mod) {
            $moduleMenu = $this->getModuleMenu($mod);
            if ($moduleMenu) {
                $menu[] = $moduleMenu;
            }
        }

        // Core menus
        $menu[] = ['label' => t('users'), 'icon' => 'fas fa-users', 'url' => '/users', 'id' => 'users'];
        $menu[] = ['label' => t('module_manager'), 'icon' => 'fas fa-puzzle-piece', 'url' => '/modules', 'id' => 'modules'];
        $menu[] = ['label' => t('settings'), 'icon' => 'fas fa-cog', 'url' => '/settings', 'id' => 'settings'];
        $menu[] = ['label' => t('audit_logs'), 'icon' => 'fas fa-history', 'url' => '/audit-logs', 'id' => 'audit'];

        $this->json(['menu' => $menu]);
    }

    private function getModuleMenu(array $mod): ?array
    {
        $name = $mod['name'] ?? '';
        $menus = [
            'crm' => ['label' => t('crm'), 'icon' => 'fas fa-handshake', 'url' => '/crm', 'children' => [
                ['label' => t('leads'), 'url' => '/crm/leads'],
                ['label' => t('opportunities'), 'url' => '/crm/opportunities'],
                ['label' => t('contacts'), 'url' => '/crm/contacts'],
            ]],
            'sales' => ['label' => t('sales'), 'icon' => 'fas fa-chart-line', 'url' => '/sales', 'children' => [
                ['label' => t('quotations'), 'url' => '/sales/quotations'],
                ['label' => t('sales_orders'), 'url' => '/sales/orders'],
                ['label' => t('invoices'), 'url' => '/sales/invoices'],
                ['label' => t('customers'), 'url' => '/sales/customers'],
                ['label' => t('products'), 'url' => '/sales/products'],
            ]],
            'purchase' => ['label' => t('purchase'), 'icon' => 'fas fa-shopping-cart', 'url' => '/purchase', 'children' => [
                ['label' => t('purchase_orders'), 'url' => '/purchase/orders'],
                ['label' => t('vendors'), 'url' => '/purchase/vendors'],
                ['label' => t('rfq'), 'url' => '/purchase/rfq'],
            ]],
            'accounting' => ['label' => t('accounting'), 'icon' => 'fas fa-calculator', 'url' => '/accounting', 'children' => [
                ['label' => t('invoices'), 'url' => '/accounting/invoices'],
                ['label' => t('payments'), 'url' => '/accounting/payments'],
                ['label' => t('journal_entries'), 'url' => '/accounting/journals'],
                ['label' => t('chart_of_accounts'), 'url' => '/accounting/accounts'],
            ]],
            'hr' => ['label' => t('hr'), 'icon' => 'fas fa-user-tie', 'url' => '/hr', 'children' => [
                ['label' => t('employees'), 'url' => '/hr/employees'],
                ['label' => t('departments'), 'url' => '/hr/departments'],
                ['label' => t('attendance'), 'url' => '/hr/attendance'],
                ['label' => t('payroll'), 'url' => '/hr/payroll'],
                ['label' => t('leave_management'), 'url' => '/hr/leaves'],
            ]],
            'inventory' => ['label' => t('inventory'), 'icon' => 'fas fa-boxes', 'url' => '/inventory', 'children' => [
                ['label' => t('products'), 'url' => '/inventory/products'],
                ['label' => t('warehouses'), 'url' => '/inventory/warehouses'],
                ['label' => t('stock_moves'), 'url' => '/inventory/moves'],
            ]],
            'projects' => ['label' => t('projects'), 'icon' => 'fas fa-project-diagram', 'url' => '/projects', 'children' => [
                ['label' => t('projects'), 'url' => '/projects'],
                ['label' => t('tasks'), 'url' => '/projects/tasks'],
                ['label' => t('timesheets'), 'url' => '/projects/timesheets'],
            ]],
            'ecommerce' => ['label' => t('ecommerce'), 'icon' => 'fas fa-shopping-bag', 'url' => '/ecommerce', 'children' => [
                ['label' => t('orders'), 'url' => '/ecommerce/orders'],
                ['label' => t('products'), 'url' => '/ecommerce/products'],
                ['label' => t('catalog'), 'url' => '/ecommerce/catalog'],
            ]],
            'pos' => ['label' => t('pos'), 'icon' => 'fas fa-cash-register', 'url' => '/pos'],
            'helpdesk' => ['label' => t('helpdesk'), 'icon' => 'fas fa-headset', 'url' => '/helpdesk', 'children' => [
                ['label' => t('tickets'), 'url' => '/helpdesk/tickets'],
            ]],
            'fleet' => ['label' => t('fleet'), 'icon' => 'fas fa-truck', 'url' => '/fleet', 'children' => [
                ['label' => t('vehicles'), 'url' => '/fleet/vehicles'],
            ]],
            'iot' => ['label' => t('iot'), 'icon' => 'fas fa-microchip', 'url' => '/iot', 'children' => [
                ['label' => t('devices'), 'url' => '/iot/devices'],
            ]],
            'quality' => ['label' => t('quality'), 'icon' => 'fas fa-clipboard-check', 'url' => '/quality', 'children' => [
                ['label' => t('quality_checks'), 'url' => '/quality/checks'],
                ['label' => t('quality_alerts'), 'url' => '/quality/alerts'],
            ]],
            'maintenance' => ['label' => t('maintenance'), 'icon' => 'fas fa-wrench', 'url' => '/maintenance', 'children' => [
                ['label' => t('maintenance_requests'), 'url' => '/maintenance/requests'],
                ['label' => t('equipment'), 'url' => '/maintenance/equipment'],
            ]],
            'studio' => ['label' => t('studio'), 'icon' => 'fas fa-paint-brush', 'url' => '/studio'],
            'prepress' => ['label' => t('prepress'), 'icon' => 'fas fa-print', 'url' => '/prepress', 'children' => [
                ['label' => t('jobs'), 'url' => '/prepress/jobs'],
                ['label' => t('proofing'), 'url' => '/prepress/proofing'],
            ]],
        ];

        return $menus[$name] ?? null;
    }
}
