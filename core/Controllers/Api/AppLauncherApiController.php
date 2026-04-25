<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class AppLauncherApiController extends BaseController
{
    public function getApps(): void
    {
        $this->requireAuth();

        $apps = [];
        $activeModules = $this->app->getModuleLoader()->getActiveModules();

        foreach ($activeModules as $mod) {
            $apps[] = [
                'name' => $mod['display_name'] ?? $mod['name'],
                'icon' => $mod['icon'] ?? 'fas fa-cube',
                'color' => $mod['color'] ?? '#875A7B',
                'url' => '/' . ($mod['directory'] ?? $mod['name']),
                'description' => $mod['description'] ?? '',
            ];
        }

        // Core apps
        array_unshift($apps, [
            'name' => t('dashboard'),
            'icon' => 'fas fa-tachometer-alt',
            'color' => '#00A09D',
            'url' => '/dashboard',
            'description' => t('dashboard_overview'),
        ]);

        $apps[] = [
            'name' => t('settings'),
            'icon' => 'fas fa-cog',
            'color' => '#6C757D',
            'url' => '/settings',
            'description' => t('general_settings'),
        ];

        $this->json(['apps' => $apps]);
    }
}
