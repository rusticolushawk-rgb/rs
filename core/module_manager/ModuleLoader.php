<?php
namespace Core\ModuleManager;

use Core\Database\Database;
use Core\Routing\Router;

class ModuleLoader
{
    private Database $db;
    private array $loadedModules = [];
    private array $availableModules = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->scanModules();
    }

    private function scanModules(): void
    {
        $modulesPath = BASE_PATH . '/modules';
        if (!is_dir($modulesPath)) return;

        $dirs = scandir($modulesPath);
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            $moduleJsonPath = $modulesPath . '/' . $dir . '/module.json';
            if (file_exists($moduleJsonPath)) {
                $config = json_decode(file_get_contents($moduleJsonPath), true);
                if ($config) {
                    $config['directory'] = $dir;
                    $config['path'] = $modulesPath . '/' . $dir;

                    // Get status from DB
                    $dbStatus = $this->getModuleDbStatus($config['name'] ?? $dir);
                    $config['installed'] = $dbStatus['installed'] ?? false;
                    $config['active'] = $dbStatus['active'] ?? false;

                    $this->availableModules[$dir] = $config;
                }
            }
        }
    }

    private function getModuleDbStatus(string $name): array
    {
        try {
            $result = $this->db->fetch(
                "SELECT * FROM modules WHERE name = ?",
                [$name]
            );
            if ($result) {
                return [
                    'installed' => true,
                    'active' => (bool) $result['is_active'],
                ];
            }
        } catch (\Exception $e) {}
        return ['installed' => false, 'active' => false];
    }

    public function loadActiveModuleRoutes(Router $router): void
    {
        foreach ($this->availableModules as $dir => $config) {
            if (!$config['active']) continue;

            $routesFile = $config['path'] . '/routes.php';
            if (file_exists($routesFile)) {
                $moduleRoutes = require $routesFile;
                if (is_callable($moduleRoutes)) {
                    $moduleRoutes($router);
                }
            }

            $this->loadedModules[$dir] = $config;
        }
    }

    public function getAvailableModules(): array
    {
        return $this->availableModules;
    }

    public function getActiveModules(): array
    {
        return array_filter($this->availableModules, fn($m) => $m['active']);
    }

    public function getInstalledModules(): array
    {
        return array_filter($this->availableModules, fn($m) => $m['installed']);
    }

    public function installModule(string $moduleName): array
    {
        $module = $this->findModule($moduleName);
        if (!$module) {
            return ['success' => false, 'error' => 'Module not found'];
        }

        // Check dependencies
        $depCheck = $this->checkDependencies($module);
        if (!$depCheck['satisfied']) {
            // Auto-install missing dependencies
            foreach ($depCheck['missing'] as $dep) {
                $result = $this->installModule($dep);
                if (!$result['success']) {
                    return ['success' => false, 'error' => "Failed to install dependency: {$dep}"];
                }
            }
        }

        // Run install SQL
        $installSql = $module['path'] . '/install.sql';
        if (file_exists($installSql)) {
            $sql = file_get_contents($installSql);
            if (!empty(trim($sql))) {
                try {
                    $this->db->getPdo()->exec($sql);
                } catch (\Exception $e) {
                    return ['success' => false, 'error' => 'SQL Error: ' . $e->getMessage()];
                }
            }
        }

        // Register in DB
        try {
            $existing = $this->db->fetch("SELECT id FROM modules WHERE name = ?", [$module['name']]);
            if ($existing) {
                $this->db->update('modules', [
                    'is_active' => 1,
                    'version' => $module['version'] ?? '1.0',
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'id = ?', [$existing['id']]);
            } else {
                $this->db->insert('modules', [
                    'name' => $module['name'],
                    'display_name' => $module['display_name'] ?? $module['name'],
                    'version' => $module['version'] ?? '1.0',
                    'category' => $module['category'] ?? 'General',
                    'description' => $module['description'] ?? '',
                    'icon' => $module['icon'] ?? 'fas fa-cube',
                    'color' => $module['color'] ?? '#875A7B',
                    'dependencies' => json_encode($module['dependencies'] ?? []),
                    'is_active' => 1,
                    'installed_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'DB Error: ' . $e->getMessage()];
        }

        // Reload modules
        $this->scanModules();

        return ['success' => true, 'message' => "Module {$moduleName} installed successfully"];
    }

    public function uninstallModule(string $moduleName): array
    {
        $module = $this->findModule($moduleName);
        if (!$module) {
            return ['success' => false, 'error' => 'Module not found'];
        }

        // Check if other modules depend on this one
        $dependents = $this->getDependents($moduleName);
        if (!empty($dependents)) {
            $depNames = implode(', ', array_column($dependents, 'name'));
            return ['success' => false, 'error' => "Cannot uninstall: required by {$depNames}"];
        }

        // Run uninstall SQL
        $uninstallSql = $module['path'] . '/uninstall.sql';
        if (file_exists($uninstallSql)) {
            $sql = file_get_contents($uninstallSql);
            if (!empty(trim($sql))) {
                try {
                    $this->db->getPdo()->exec($sql);
                } catch (\Exception $e) {
                    // Log but continue
                }
            }
        }

        // Update DB
        $this->db->update('modules', [
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'name = ?', [$moduleName]);

        $this->scanModules();

        return ['success' => true, 'message' => "Module {$moduleName} uninstalled"];
    }

    public function activateModule(string $moduleName): array
    {
        $module = $this->findModule($moduleName);
        if (!$module || !$module['installed']) {
            return ['success' => false, 'error' => 'Module not installed'];
        }

        $this->db->update('modules', ['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')], 'name = ?', [$moduleName]);
        $this->scanModules();

        return ['success' => true, 'message' => "Module {$moduleName} activated"];
    }

    public function deactivateModule(string $moduleName): array
    {
        $dependents = $this->getDependents($moduleName);
        $activeDependents = array_filter($dependents, fn($d) => $d['active']);
        if (!empty($activeDependents)) {
            $depNames = implode(', ', array_column($activeDependents, 'name'));
            return ['success' => false, 'error' => "Cannot deactivate: required by {$depNames}"];
        }

        $this->db->update('modules', ['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')], 'name = ?', [$moduleName]);
        $this->scanModules();

        return ['success' => true, 'message' => "Module {$moduleName} deactivated"];
    }

    public function checkDependencies(array $module): array
    {
        $dependencies = $module['dependencies'] ?? [];
        $missing = [];

        foreach ($dependencies as $dep) {
            $depModule = $this->findModule($dep);
            if (!$depModule || !$depModule['installed'] || !$depModule['active']) {
                $missing[] = $dep;
            }
        }

        return [
            'satisfied' => empty($missing),
            'missing' => $missing,
        ];
    }

    private function getDependents(string $moduleName): array
    {
        $dependents = [];
        foreach ($this->availableModules as $module) {
            $deps = $module['dependencies'] ?? [];
            if (in_array($moduleName, $deps) && $module['installed']) {
                $dependents[] = $module;
            }
        }
        return $dependents;
    }

    private function findModule(string $name): ?array
    {
        foreach ($this->availableModules as $module) {
            if ($module['name'] === $name || $module['directory'] === $name) {
                return $module;
            }
        }
        return null;
    }

    public function getModulesByCategory(): array
    {
        $grouped = [];
        foreach ($this->availableModules as $module) {
            $cat = $module['category'] ?? 'General';
            $grouped[$cat][] = $module;
        }
        return $grouped;
    }
}
