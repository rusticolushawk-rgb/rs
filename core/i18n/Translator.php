<?php
namespace Core\I18n;

class Translator
{
    private string $locale;
    private array $translations = [];
    private static ?Translator $instance = null;

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
        self::$instance = $this;
    }

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    private function loadTranslations(): void
    {
        $file = BASE_PATH . '/lang/' . $this->locale . '.php';
        if (file_exists($file)) {
            $this->translations = require $file;
        }
    }

    public function translate(string $key, array $params = []): string
    {
        $text = $this->translations[$key] ?? $key;
        foreach ($params as $param => $value) {
            $text = str_replace(':' . $param, $value, $text);
        }
        return $text;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public function isRtl(): bool
    {
        return $this->locale === 'ar';
    }

    public function getDirection(): string
    {
        return $this->isRtl() ? 'rtl' : 'ltr';
    }

    public function getAllTranslations(): array
    {
        return $this->translations;
    }
}
