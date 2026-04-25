<?php
namespace Core;

use Core\Database\Database;
use Core\Routing\Router;
use Core\Auth\Auth;
use Core\I18n\Translator;
use Core\Currency\CurrencyManager;
use Core\ModuleManager\ModuleLoader;

class App
{
    private static ?App $instance = null;
    private array $config = [];
    private Database $db;
    private Router $router;
    private Auth $auth;
    private Translator $translator;
    private CurrencyManager $currency;
    private ModuleLoader $moduleLoader;

    private function __construct()
    {
        $this->loadConfig();
        $this->initDatabase();
        $this->initSession();
        $this->initTranslator();
        $this->initCurrency();
        $this->initAuth();
        $this->initModuleLoader();
        $this->initRouter();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        $this->config['app'] = require BASE_PATH . '/config/app.php';
        $this->config['database'] = require BASE_PATH . '/config/database.php';
    }

    private function initDatabase(): void
    {
        $this->db = Database::getInstance($this->config['database']);
    }

    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function initTranslator(): void
    {
        $locale = $_SESSION['locale'] ?? $_COOKIE['locale'] ?? $this->config['app']['locale'];
        $this->translator = new Translator($locale);
    }

    private function initCurrency(): void
    {
        $this->currency = new CurrencyManager($this->db);
    }

    private function initAuth(): void
    {
        $this->auth = new Auth($this->db);
    }

    private function initModuleLoader(): void
    {
        $this->moduleLoader = new ModuleLoader($this->db);
    }

    private function initRouter(): void
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Handle API requests
        if (str_starts_with($uri, '/api/')) {
            header('Content-Type: application/json');
        }

        // Register core routes
        $this->registerCoreRoutes();

        // Load module routes
        $this->moduleLoader->loadActiveModuleRoutes($this->router);

        // Dispatch request
        $this->router->dispatch($method, $uri);
    }

    private function registerCoreRoutes(): void
    {
        // Auth routes
        $this->router->get('/', 'Core\\Controllers\\DashboardController@index');
        $this->router->get('/login', 'Core\\Controllers\\AuthController@loginForm');
        $this->router->post('/login', 'Core\\Controllers\\AuthController@login');
        $this->router->get('/logout', 'Core\\Controllers\\AuthController@logout');

        // Dashboard
        $this->router->get('/dashboard', 'Core\\Controllers\\DashboardController@index');

        // Settings
        $this->router->get('/settings', 'Core\\Controllers\\SettingsController@index');
        $this->router->post('/settings/company', 'Core\\Controllers\\SettingsController@updateCompany');
        $this->router->post('/settings/logo', 'Core\\Controllers\\SettingsController@uploadLogo');

        // Module Manager
        $this->router->get('/modules', 'Core\\Controllers\\ModuleManagerController@index');
        $this->router->post('/modules/install', 'Core\\Controllers\\ModuleManagerController@install');
        $this->router->post('/modules/uninstall', 'Core\\Controllers\\ModuleManagerController@uninstall');
        $this->router->post('/modules/activate', 'Core\\Controllers\\ModuleManagerController@activate');
        $this->router->post('/modules/deactivate', 'Core\\Controllers\\ModuleManagerController@deactivate');

        // API routes
        $this->router->get('/api/modules', 'Core\\Controllers\\Api\\ModuleApiController@list');
        $this->router->post('/api/modules/install', 'Core\\Controllers\\Api\\ModuleApiController@install');
        $this->router->post('/api/modules/uninstall', 'Core\\Controllers\\Api\\ModuleApiController@uninstall');
        $this->router->get('/api/dashboard/stats', 'Core\\Controllers\\Api\\DashboardApiController@stats');
        $this->router->get('/api/dashboard/charts', 'Core\\Controllers\\Api\\DashboardApiController@charts');
        $this->router->post('/api/lang/switch', 'Core\\Controllers\\Api\\LangApiController@switchLang');
        $this->router->get('/api/currencies', 'Core\\Controllers\\Api\\CurrencyApiController@list');
        $this->router->post('/api/currencies/convert', 'Core\\Controllers\\Api\\CurrencyApiController@convert');
        $this->router->get('/api/notifications', 'Core\\Controllers\\Api\\NotificationApiController@list');
        $this->router->post('/api/notifications/read', 'Core\\Controllers\\Api\\NotificationApiController@markRead');
        $this->router->get('/api/sidebar', 'Core\\Controllers\\Api\\SidebarApiController@getMenu');
        $this->router->get('/api/app-launcher', 'Core\\Controllers\\Api\\AppLauncherApiController@getApps');

        // User Management
        $this->router->get('/users', 'Core\\Controllers\\UserController@index');
        $this->router->get('/users/create', 'Core\\Controllers\\UserController@create');
        $this->router->post('/users', 'Core\\Controllers\\UserController@store');
        $this->router->get('/users/{id}', 'Core\\Controllers\\UserController@show');
        $this->router->post('/users/{id}', 'Core\\Controllers\\UserController@update');
        $this->router->post('/users/{id}/delete', 'Core\\Controllers\\UserController@delete');

        // Audit Logs
        $this->router->get('/audit-logs', 'Core\\Controllers\\AuditLogController@index');
        $this->router->get('/api/audit-logs', 'Core\\Controllers\\Api\\AuditLogApiController@list');
    }

    public function getConfig(string $key = null)
    {
        if ($key === null) return $this->config;
        $keys = explode('.', $key);
        $value = $this->config;
        foreach ($keys as $k) {
            $value = $value[$k] ?? null;
        }
        return $value;
    }

    public function getDb(): Database { return $this->db; }
    public function getRouter(): Router { return $this->router; }
    public function getAuth(): Auth { return $this->auth; }
    public function getTranslator(): Translator { return $this->translator; }
    public function getCurrency(): CurrencyManager { return $this->currency; }
    public function getModuleLoader(): ModuleLoader { return $this->moduleLoader; }
}
