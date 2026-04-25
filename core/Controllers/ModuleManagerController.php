<?php
namespace Core\Controllers;

class ModuleManagerController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $moduleLoader = $this->app->getModuleLoader();
        $modules = $moduleLoader->getAvailableModules();
        $modulesByCategory = $moduleLoader->getModulesByCategory();
        $categories = $this->app->getDb()->fetchAll("SELECT * FROM categories ORDER BY sort_order");

        $this->view('module_manager.index', [
            'modules' => $modules,
            'modulesByCategory' => $modulesByCategory,
            'categories' => $categories,
            'pageTitle' => t('module_manager'),
            'breadcrumb' => [['label' => t('module_manager'), 'url' => '/modules']],
        ]);
    }

    public function install(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');

        $result = $this->app->getModuleLoader()->installModule($moduleName);
        $this->audit('install', 'modules', 0, "Install module: {$moduleName}");

        if ($this->isAjax()) {
            $this->json($result);
        }
        $this->redirect('/modules');
    }

    public function uninstall(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');

        $result = $this->app->getModuleLoader()->uninstallModule($moduleName);
        $this->audit('uninstall', 'modules', 0, "Uninstall module: {$moduleName}");

        if ($this->isAjax()) {
            $this->json($result);
        }
        $this->redirect('/modules');
    }

    public function activate(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');

        $result = $this->app->getModuleLoader()->activateModule($moduleName);

        if ($this->isAjax()) {
            $this->json($result);
        }
        $this->redirect('/modules');
    }

    public function deactivate(): void
    {
        $this->requirePermission('admin');
        $moduleName = $this->input('module');

        $result = $this->app->getModuleLoader()->deactivateModule($moduleName);

        if ($this->isAjax()) {
            $this->json($result);
        }
        $this->redirect('/modules');
    }
}
