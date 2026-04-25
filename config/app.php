<?php
/**
 * Gyr Falcon ERP - Application Configuration
 */
return [
    'name' => 'Gyr Falcon ERP',
    'version' => '1.0.0',
    'url' => 'http://localhost:8080',
    'debug' => true,
    'timezone' => 'UTC',
    'locale' => 'en',
    'supported_locales' => ['en', 'ar'],
    'default_currency' => 'EGP',
    'supported_currencies' => ['EGP', 'USD', 'EUR'],
    'items_per_page' => 25,
    'upload_max_size' => 10485760, // 10MB
    'upload_allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
];
