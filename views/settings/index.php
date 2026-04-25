<?php ob_start(); ?>

<h2 style="font-size:18px; font-weight:600; margin-bottom:16px;">
    <i class="fas fa-cog"></i> <?= $t('settings') ?>
</h2>

<div class="settings-layout">
    <!-- Settings Navigation -->
    <div class="settings-nav">
        <div class="settings-nav-item active" onclick="GyrFalcon.switchTab('companySettings'); document.querySelectorAll('.settings-nav-item').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
            <i class="fas fa-building"></i> <?= $t('company_profile') ?>
        </div>
        <div class="settings-nav-item" onclick="GyrFalcon.switchTab('currencySettings'); document.querySelectorAll('.settings-nav-item').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
            <i class="fas fa-money-bill-wave"></i> <?= $t('currencies') ?>
        </div>
        <div class="settings-nav-item" onclick="GyrFalcon.switchTab('generalSettings'); document.querySelectorAll('.settings-nav-item').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
            <i class="fas fa-sliders-h"></i> <?= $t('general_settings') ?>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="settings-content">
        <!-- Company Profile Tab -->
        <div id="companySettings" class="tab-content active">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-building"></i> <?= $t('company_profile') ?></h3>
                </div>
                <div class="card-body">
                    <form id="companyForm" method="POST" action="/settings/company">
                        <?= csrf_field() ?>

                        <!-- Logo Upload -->
                        <div class="form-group" style="margin-bottom:24px;">
                            <label><?= $t('company_logo') ?></label>
                            <div style="display:flex; align-items:center; gap:16px;">
                                <?php if (!empty($company['logo'])): ?>
                                    <img id="logoPreview" src="<?= e($company['logo']) ?>" alt="Logo" 
                                         style="width:80px; height:80px; object-fit:contain; border:1px solid var(--gray-300); border-radius:8px; padding:4px;">
                                <?php else: ?>
                                    <div id="logoPreview" style="width:80px; height:80px; background:var(--gray-100); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--gray-400);">
                                        <i class="fas fa-image fa-2x"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <input type="file" id="logoInput" name="logo" accept="image/*" 
                                           onchange="uploadLogo(this)" style="display:none">
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('logoInput').click()">
                                        <i class="fas fa-upload"></i> <?= $t('upload_logo') ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('company_name') ?></label>
                                <input type="text" name="name" class="form-control" value="<?= e($company['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label><?= $t('tax_id') ?></label>
                                <input type="text" name="tax_id" class="form-control" value="<?= e($company['tax_id'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('registration_number') ?></label>
                                <input type="text" name="registration_number" class="form-control" value="<?= e($company['registration_number'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $t('company_website') ?></label>
                                <input type="url" name="website" class="form-control" value="<?= e($company['website'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-section-title mt-2"><?= $t('address') ?></div>
                        <div class="form-group">
                            <label><?= $t('company_address') ?></label>
                            <textarea name="address" class="form-control" rows="2"><?= e($company['address'] ?? '') ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('city') ?></label>
                                <input type="text" name="city" class="form-control" value="<?= e($company['city'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $t('state') ?></label>
                                <input type="text" name="state" class="form-control" value="<?= e($company['state'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('country') ?></label>
                                <input type="text" name="country" class="form-control" value="<?= e($company['country'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $t('postal_code') ?></label>
                                <input type="text" name="postal_code" class="form-control" value="<?= e($company['postal_code'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-section-title mt-2"><?= $t('contacts') ?></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('company_phone') ?></label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($company['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $t('company_email') ?></label>
                                <input type="email" name="email" class="form-control" value="<?= e($company['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-section-title mt-2"><?= $t('general_settings') ?></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label><?= $t('default_currency') ?></label>
                                <select name="default_currency" class="form-control">
                                    <?php foreach ($currencies ?? [] as $curr): ?>
                                        <option value="<?= e($curr['code']) ?>" <?= ($company['default_currency'] ?? 'EGP') === $curr['code'] ? 'selected' : '' ?>>
                                            <?= e($curr['symbol'] . ' ' . $curr['name'] . ' (' . $curr['code'] . ')') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= $t('language_preference') ?></label>
                                <select name="language" class="form-control">
                                    <option value="en" <?= ($company['language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="ar" <?= ($company['language'] ?? 'en') === 'ar' ? 'selected' : '' ?>>العربية</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $t('save') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Currency Settings Tab -->
        <div id="currencySettings" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-money-bill-wave"></i> <?= $t('currencies') ?> & <?= $t('exchange_rates') ?></h3>
                </div>
                <div class="card-body">
                    <!-- Active Currencies -->
                    <h4 class="mb-1"><?= $t('currencies') ?></h4>
                    <div class="table-container mb-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?= $t('currency_code') ?></th>
                                    <th><?= $t('name') ?></th>
                                    <th><?= $t('currency_symbol') ?></th>
                                    <th><?= $t('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($currencies ?? [] as $curr): ?>
                                    <tr>
                                        <td><strong><?= e($curr['code']) ?></strong></td>
                                        <td><?= e($curr['name']) ?></td>
                                        <td><?= e($curr['symbol']) ?></td>
                                        <td><span class="badge badge-success"><?= $t('active') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Exchange Rates -->
                    <h4 class="mb-1"><?= $t('exchange_rates') ?></h4>
                    <div class="table-container mb-2">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?= $t('from_currency') ?></th>
                                    <th><?= $t('to_currency') ?></th>
                                    <th><?= $t('rate') ?></th>
                                    <th><?= $t('date') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exchangeRates ?? [] as $rate): ?>
                                    <tr>
                                        <td><strong><?= e($rate['from_currency']) ?></strong></td>
                                        <td><strong><?= e($rate['to_currency']) ?></strong></td>
                                        <td><?= e($rate['rate']) ?></td>
                                        <td><?= e($rate['effective_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Currency Converter -->
                    <div class="card" style="background:var(--gray-50);">
                        <div class="card-header">
                            <h3><i class="fas fa-exchange-alt"></i> <?= $t('convert') ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label><?= $t('amount') ?></label>
                                    <input type="number" id="convertAmount" class="form-control" value="1000" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label><?= $t('from_currency') ?></label>
                                    <select id="convertFrom" class="form-control">
                                        <?php foreach ($currencies ?? [] as $curr): ?>
                                            <option value="<?= e($curr['code']) ?>"><?= e($curr['code'] . ' - ' . $curr['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><?= $t('to_currency') ?></label>
                                    <select id="convertTo" class="form-control">
                                        <?php foreach ($currencies ?? [] as $curr): ?>
                                            <option value="<?= e($curr['code']) ?>" <?= $curr['code'] === 'USD' ? 'selected' : '' ?>><?= e($curr['code'] . ' - ' . $curr['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" onclick="GyrFalcon.convertCurrency()">
                                <i class="fas fa-exchange-alt"></i> <?= $t('convert') ?>
                            </button>
                            <div id="conversionResult" class="alert alert-info mt-1" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Settings Tab -->
        <div id="generalSettings" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-sliders-h"></i> <?= $t('general_settings') ?></h3>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Value</th>
                                    <th>Group</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($settings ?? [] as $key => $value): ?>
                                    <tr>
                                        <td><strong><?= e($key) ?></strong></td>
                                        <td><?= e($value) ?></td>
                                        <td><span class="badge badge-secondary">general</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function uploadLogo(input) {
    if (!input.files || !input.files[0]) return;
    const formData = new FormData();
    formData.append('logo', input.files[0]);
    
    fetch('/settings/logo', { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.logo) {
                const preview = document.getElementById('logoPreview');
                if (preview.tagName === 'IMG') {
                    preview.src = data.logo;
                } else {
                    preview.outerHTML = '<img id="logoPreview" src="' + data.logo + '" style="width:80px;height:80px;object-fit:contain;border:1px solid var(--gray-300);border-radius:8px;padding:4px;">';
                }
                GyrFalcon.showToast('Logo updated successfully', 'success');
            }
        });
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
