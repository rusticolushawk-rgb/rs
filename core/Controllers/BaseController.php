<?php
namespace Core\Controllers;

use Core\App;

class BaseController
{
    protected App $app;

    public function __construct()
    {
        $this->app = App::getInstance();
    }

    protected function view(string $view, array $data = []): void
    {
        $app = $this->app;
        $auth = $app->getAuth();
        $translator = $app->getTranslator();
        $currency = $app->getCurrency();
        $moduleLoader = $app->getModuleLoader();

        // Make variables available to view
        $data['app'] = $app;
        $data['auth'] = $auth;
        $data['user'] = $auth->user();
        $data['t'] = fn(string $key, array $params = []) => $translator->translate($key, $params);
        $data['locale'] = $translator->getLocale();
        $data['dir'] = $translator->getDirection();
        $data['isRtl'] = $translator->isRtl();
        $data['currencies'] = $currency->getCurrencies();
        $data['activeModules'] = $moduleLoader->getActiveModules();

        // Company info
        try {
            $data['company'] = $app->getDb()->fetch("SELECT * FROM companies WHERE is_default = 1 LIMIT 1");
        } catch (\Exception $e) {
            $data['company'] = null;
        }

        extract($data);

        $viewPath = BASE_PATH . '/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found at {$viewPath}");
        }

        include $viewPath;
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!$this->app->getAuth()->check()) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Unauthorized'], 401);
            }
            $this->redirect('/login');
        }
    }

    protected function requirePermission(string $permission): void
    {
        $this->requireAuth();
        if (!$this->app->getAuth()->hasPermission($permission)) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Forbidden'], 403);
            }
            http_response_code(403);
            echo 'Access Denied';
            exit;
        }
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function input(string $key, $default = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $_GET[$key] ?? $default;
        }

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            return $body[$key] ?? $default;
        }

        return $_POST[$key] ?? $default;
    }

    protected function allInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        return array_merge($_GET, $_POST);
    }

    protected function audit(string $action, string $entityType, int $entityId, string $description = ''): void
    {
        try {
            $userId = $this->app->getAuth()->id() ?? 0;
            $this->app->getDb()->insert('audit_logs', [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {}
    }
}
