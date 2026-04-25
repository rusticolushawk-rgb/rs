<?php
/**
 * Gyr Falcon ERP - Application Entry Point
 */

define('BASE_PATH', dirname(__DIR__));

// Autoloader
spl_autoload_register(function ($class) {
    // Map namespace prefixes to directories
    $prefixes = [
        'Core\\' => BASE_PATH . '/core/',
        'Modules\\' => BASE_PATH . '/modules/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) continue;

        $relativeClass = substr($class, $len);
        // Try exact match first
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }

        // Try lowercase directory match
        $parts = explode('\\', $relativeClass);
        $className = array_pop($parts);
        $dirParts = array_map('strtolower', $parts);
        $file = $baseDir . implode('/', $dirParts) . '/' . $className . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }

        // Try snake_case directory match (e.g., ModuleManager -> module_manager)
        $snakeParts = array_map(function ($p) {
            return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $p));
        }, $parts);
        $file = $baseDir . implode('/', $snakeParts) . '/' . $className . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

// Load helpers
require_once BASE_PATH . '/core/helpers/functions.php';

// Error handling
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function ($e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    $isApi = str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');

    if ($isApi) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Internal Server Error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    } else {
        http_response_code(500);
        echo '<h1>Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
    }
});

// Run Application
try {
    $app = \Core\App::getInstance();
    $app->run();
} catch (\Exception $e) {
    error_log('Fatal: ' . $e->getMessage());
    echo '<h1>Application Error</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
