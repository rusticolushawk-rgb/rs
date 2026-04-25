<?php ob_start(); ?>

<div class="view-toolbar">
    <div class="view-toolbar-left">
        <h2 style="font-size:18px; font-weight:600;">
            <i class="fas fa-puzzle-piece"></i> <?= $t('module_manager') ?>
        </h2>
    </div>
    <div class="view-toolbar-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="<?= $t('search') ?> <?= $t('modules') ?>..." 
                   oninput="filterModuleCards(this.value)">
        </div>
    </div>
</div>

<!-- Category Filter -->
<div class="filter-panel">
    <span class="filter-tag active" onclick="filterByCategory('all', this)"><?= $t('all_categories') ?></span>
    <?php foreach ($categories ?? [] as $cat): ?>
        <span class="filter-tag" onclick="filterByCategory('<?= e($cat['slug']) ?>', this)">
            <i class="<?= e($cat['icon'] ?? 'fas fa-folder') ?>" style="color:<?= e($cat['color'] ?? '#666') ?>"></i>
            <?= e($cat['name']) ?>
        </span>
    <?php endforeach; ?>
</div>

<!-- Tabs: All / Installed -->
<div class="tabs">
    <button class="tab-btn active" data-tab="allModules" onclick="GyrFalcon.switchTab('allModules')">
        <?= $t('available_modules') ?> (<?= count($modules ?? []) ?>)
    </button>
    <button class="tab-btn" data-tab="installedModules" onclick="GyrFalcon.switchTab('installedModules')">
        <?= $t('installed_modules') ?> (<?= count(array_filter($modules ?? [], fn($m) => $m['installed'])) ?>)
    </button>
</div>

<!-- All Modules Grid -->
<div id="allModules" class="tab-content active">
    <div class="module-store-grid" id="moduleGrid">
        <?php foreach ($modules ?? [] as $mod): ?>
            <div class="module-card" data-category="<?= e($mod['category'] ?? '') ?>" data-name="<?= e(strtolower($mod['display_name'] ?? $mod['name'])) ?>">
                <div class="module-card-header">
                    <div class="module-card-icon" style="background:<?= e($mod['color'] ?? '#875A7B') ?>">
                        <i class="<?= e($mod['icon'] ?? 'fas fa-cube') ?>"></i>
                    </div>
                    <div class="module-card-info">
                        <h4><?= e($mod['display_name'] ?? $mod['name']) ?></h4>
                        <span class="module-version">v<?= e($mod['version'] ?? '1.0') ?></span>
                        <span class="module-category"><?= e($mod['category'] ?? 'General') ?></span>
                    </div>
                </div>
                <div class="module-card-body">
                    <p><?= e($mod['description'] ?? '') ?></p>
                </div>
                <div class="module-card-footer">
                    <div class="module-deps">
                        <?php
                        $deps = $mod['dependencies'] ?? [];
                        if (empty($deps)) {
                            echo '<small>' . $t('no_dependencies') . '</small>';
                        } else {
                            echo '<small><i class="fas fa-link"></i> ' . implode(', ', $deps) . '</small>';
                        }
                        ?>
                    </div>
                    <?php if ($mod['active']): ?>
                        <span class="badge badge-success"><i class="fas fa-check"></i> <?= $t('active') ?></span>
                    <?php elseif ($mod['installed']): ?>
                        <button class="btn btn-sm btn-info" onclick="GyrFalcon.installModule('<?= e($mod['name']) ?>')">
                            <i class="fas fa-play"></i> <?= $t('activate') ?>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-sm btn-primary" onclick="GyrFalcon.installModule('<?= e($mod['name']) ?>')">
                            <i class="fas fa-download"></i> <?= $t('install') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($modules)): ?>
        <div class="empty-state">
            <i class="fas fa-puzzle-piece"></i>
            <h3><?= $t('no_data') ?></h3>
            <p>No modules found in the modules directory.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Installed Modules -->
<div id="installedModules" class="tab-content">
    <div class="module-store-grid">
        <?php foreach ($modules ?? [] as $mod): ?>
            <?php if (!$mod['installed']) continue; ?>
            <div class="module-card">
                <div class="module-card-header">
                    <div class="module-card-icon" style="background:<?= e($mod['color'] ?? '#875A7B') ?>">
                        <i class="<?= e($mod['icon'] ?? 'fas fa-cube') ?>"></i>
                    </div>
                    <div class="module-card-info">
                        <h4><?= e($mod['display_name'] ?? $mod['name']) ?></h4>
                        <span class="module-version">v<?= e($mod['version'] ?? '1.0') ?></span>
                    </div>
                </div>
                <div class="module-card-footer">
                    <span class="badge badge-success"><?= $t('installed') ?></span>
                    <button class="btn btn-sm btn-danger" onclick="GyrFalcon.uninstallModule('<?= e($mod['name']) ?>')">
                        <i class="fas fa-trash"></i> <?= $t('uninstall') ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function filterModuleCards(search) {
    const cards = document.querySelectorAll('.module-card');
    const s = search.toLowerCase();
    cards.forEach(card => {
        const name = card.dataset.name || '';
        card.style.display = name.includes(s) ? '' : 'none';
    });
}

function filterByCategory(category, el) {
    document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    
    const cards = document.querySelectorAll('.module-card');
    cards.forEach(card => {
        if (category === 'all') {
            card.style.display = '';
        } else {
            const cardCat = (card.dataset.category || '').toLowerCase().replace(/[&\s]+/g, '-');
            card.style.display = cardCat.includes(category) ? '' : 'none';
        }
    });
}
</script>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
