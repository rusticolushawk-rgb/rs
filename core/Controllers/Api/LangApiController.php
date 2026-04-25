<?php
namespace Core\Controllers\Api;

use Core\Controllers\BaseController;

class LangApiController extends BaseController
{
    public function switchLang(): void
    {
        $locale = $this->input('locale', 'en');
        $supported = ['en', 'ar'];

        if (!in_array($locale, $supported)) {
            $this->json(['success' => false, 'error' => 'Unsupported locale'], 400);
        }

        $_SESSION['locale'] = $locale;
        setcookie('locale', $locale, time() + 365 * 24 * 3600, '/');

        // Load translations for the new locale
        $file = BASE_PATH . '/lang/' . $locale . '.php';
        $translations = file_exists($file) ? require $file : [];

        $this->json([
            'success' => true,
            'locale' => $locale,
            'direction' => $locale === 'ar' ? 'rtl' : 'ltr',
            'translations' => $translations,
        ]);
    }
}
