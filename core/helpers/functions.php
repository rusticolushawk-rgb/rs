<?php
/**
 * Global helper functions for Gyr Falcon ERP
 */

use Core\App;
use Core\I18n\Translator;

function app(): App
{
    return App::getInstance();
}

function t(string $key, array $params = []): string
{
    $translator = Translator::getInstance();
    return $translator ? $translator->translate($key, $params) : $key;
}

function db(): \Core\Database\Database
{
    return app()->getDb();
}

function auth(): \Core\Auth\Auth
{
    return app()->getAuth();
}

function config(string $key = null)
{
    return app()->getConfig($key);
}

function url(string $path = ''): string
{
    return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

function old(string $key, $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

function flash(string $key, $value = null)
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function formatCurrency(float $amount, string $currency = 'EGP'): string
{
    return app()->getCurrency()->format($amount, $currency);
}

function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', $time);
}

function generateSlug(string $text): string
{
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return strtolower(trim($text, '-'));
}
