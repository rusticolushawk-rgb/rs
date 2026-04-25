<?php
namespace Core\Controllers;

class SettingsController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $company = $db->fetch("SELECT * FROM companies WHERE is_default = 1 LIMIT 1");
        $currencies = $db->fetchAll("SELECT * FROM currencies WHERE is_active = 1");
        $exchangeRates = $db->fetchAll(
            "SELECT * FROM exchange_rates ORDER BY effective_date DESC LIMIT 20"
        );
        $settings = $db->fetchAll("SELECT * FROM settings");
        $settingsMap = [];
        foreach ($settings as $s) {
            $settingsMap[$s['setting_key']] = $s['setting_value'];
        }

        $this->view('settings.index', [
            'company' => $company,
            'currencies' => $currencies,
            'exchangeRates' => $exchangeRates,
            'settings' => $settingsMap,
            'pageTitle' => t('settings'),
            'breadcrumb' => [['label' => t('settings'), 'url' => '/settings']],
        ]);
    }

    public function updateCompany(): void
    {
        $this->requireAuth();
        $db = $this->app->getDb();

        $data = [
            'name' => $this->input('name', ''),
            'address' => $this->input('address', ''),
            'city' => $this->input('city', ''),
            'state' => $this->input('state', ''),
            'country' => $this->input('country', ''),
            'postal_code' => $this->input('postal_code', ''),
            'phone' => $this->input('phone', ''),
            'email' => $this->input('email', ''),
            'website' => $this->input('website', ''),
            'tax_id' => $this->input('tax_id', ''),
            'registration_number' => $this->input('registration_number', ''),
            'default_currency' => $this->input('default_currency', 'EGP'),
            'language' => $this->input('language', 'en'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $company = $db->fetch("SELECT id FROM companies WHERE is_default = 1 LIMIT 1");
        if ($company) {
            $db->update('companies', $data, 'id = ?', [$company['id']]);
        } else {
            $data['is_default'] = 1;
            $data['is_active'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $db->insert('companies', $data);
        }

        $this->audit('update', 'companies', $company['id'] ?? 0, 'Updated company settings');

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => t('settings_saved')]);
        }
        $this->redirect('/settings');
    }

    public function uploadLogo(): void
    {
        $this->requireAuth();

        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'No file uploaded'], 400);
            }
            $this->redirect('/settings');
        }

        $file = $_FILES['logo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Invalid file type'], 400);
            }
            $this->redirect('/settings');
        }

        $filename = 'logo_' . time() . '.' . $ext;
        $uploadDir = BASE_PATH . '/public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $db = $this->app->getDb();
            $db->update('companies', ['logo' => '/public/uploads/' . $filename], 'is_default = 1', []);

            if ($this->isAjax()) {
                $this->json(['success' => true, 'logo' => '/public/uploads/' . $filename]);
            }
        }

        $this->redirect('/settings');
    }
}
