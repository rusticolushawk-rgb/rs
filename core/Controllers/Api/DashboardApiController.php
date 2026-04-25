<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class DashboardApiController extends BaseController
{
    public function stats(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $stats = [
            'modules' => $db->count('modules', 'is_active = 1'),
            'users' => $db->count('users', 'is_active = 1'),
            'companies' => $db->count('companies', 'is_active = 1'),
            'audit_entries' => $db->count('audit_logs'),
        ];

        // Gather module-specific stats
        $activeModules = $this->app->getModuleLoader()->getActiveModules();
        foreach ($activeModules as $mod) {
            $name = $mod['name'] ?? '';
            try {
                switch ($name) {
                    case 'crm':
                        $stats['leads'] = $db->count('crm_leads');
                        $stats['opportunities'] = $db->count('crm_opportunities');
                        break;
                    case 'sales':
                        $stats['orders'] = $db->count('sales_orders');
                        $stats['revenue'] = $db->fetch("SELECT COALESCE(SUM(total_amount),0) as total FROM sales_orders WHERE status != 'cancelled'")['total'] ?? 0;
                        break;
                    case 'hr':
                        $stats['employees'] = $db->count('hr_employees', 'is_active = 1');
                        break;
                    case 'inventory':
                        $stats['products'] = $db->count('inventory_products');
                        break;
                    case 'helpdesk':
                        $stats['tickets'] = $db->count('helpdesk_tickets');
                        break;
                }
            } catch (\Exception $e) {
                // Table might not exist
            }
        }

        $this->json(['stats' => $stats]);
    }

    public function charts(): void
    {
        $this->requireAuth();

        // Generate sample chart data
        $months = [];
        $revenue = [];
        $orders = [];
        for ($i = 5; $i >= 0; $i--) {
            $months[] = date('M Y', strtotime("-{$i} months"));
            $revenue[] = rand(50000, 200000);
            $orders[] = rand(50, 300);
        }

        $this->json([
            'revenue_chart' => [
                'labels' => $months,
                'datasets' => [
                    ['label' => t('total_revenue'), 'data' => $revenue, 'borderColor' => '#875A7B', 'fill' => false],
                ]
            ],
            'orders_chart' => [
                'labels' => $months,
                'datasets' => [
                    ['label' => t('total_orders'), 'data' => $orders, 'backgroundColor' => '#00A09D'],
                ]
            ],
        ]);
    }
}
