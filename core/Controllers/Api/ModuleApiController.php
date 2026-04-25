<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class ModuleApiController extends BaseController
{
    public function list(): void
    {
        $this->requireAuth();
        $modules = $this->app->getModuleLoader()->getAvailableModules();
        $this->json(['modules' => array_values($modules)]);
    }

    public function install(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');
        $result = $this->app->getModuleLoader()->installModule($moduleName);
        $this->json($result, $result['success'] ? 200 : 400);
    }

    public function uninstall(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');
        $result = $this->app->getModuleLoader()->uninstallModule($moduleName);
        $this->json($result, $result['success'] ? 200 : 400);
    }
}
