<!DOCTYPE html>
<html lang="<?= $locale ?? 'en' ?>" dir="<?= $dir ?? 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — <?= $t('app_name') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" rel="stylesheet">
    <style><?php include BASE_PATH . '/public/css/app.css'; ?></style>
</head>
<body class="<?= ($isRtl ?? false) ? 'rtl' : 'ltr' ?>" data-locale="<?= $locale ?? 'en' ?>">

<!-- App Launcher Modal -->
<div id="appLauncherModal" class="modal-overlay" style="display:none;">
    <div class="app-launcher-modal">
        <div class="app-launcher-header">
            <h3><i class="fas fa-th"></i> <?= $t('app_launcher') ?></h3>
            <button class="btn-icon" onclick="toggleAppLauncher()"><i class="fas fa-times"></i></button>
        </div>
        <div class="app-launcher-search">
            <input type="text" id="appSearchInput" placeholder="<?= $t('search') ?>..." oninput="filterApps(this.value)">
        </div>
        <div id="appLauncherGrid" class="app-launcher-grid">
            <!-- Dynamic apps will be loaded here -->
        </div>
    </div>
</div>

<!-- Top Navigation Bar -->
<nav class="top-navbar">
    <div class="navbar-left">
        <button class="btn-icon sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <a href="/dashboard" class="navbar-brand">
            <?php if (!empty($company['logo'])): ?>
                <img src="<?= e($company['logo']) ?>" alt="Logo" class="navbar-logo">
            <?php else: ?>
                <i class="fas fa-feather-alt brand-icon"></i>
            <?php endif; ?>
            <span class="brand-text"><?= e($company['name'] ?? $t('app_name')) ?></span>
        </a>
    </div>

    <div class="navbar-center">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <?php if (!empty($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $i => $crumb): ?>
                    <?php if ($i > 0): ?><i class="fas fa-chevron-right breadcrumb-sep"></i><?php endif; ?>
                    <a href="<?= e($crumb['url'] ?? '#') ?>" class="breadcrumb-item <?= $i === count($breadcrumb) - 1 ? 'active' : '' ?>">
                        <?= e($crumb['label']) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="navbar-right">
        <!-- App Launcher Button -->
        <button class="btn-icon" onclick="toggleAppLauncher()" title="<?= $t('app_launcher') ?>">
            <i class="fas fa-th"></i>
        </button>

        <!-- Language Switch -->
        <button class="btn-icon" onclick="switchLanguage()" title="<?= $t('switch_language') ?>">
            <span class="lang-badge"><?= strtoupper($locale ?? 'en') ?></span>
        </button>

        <!-- Notifications -->
        <div class="nav-dropdown">
            <button class="btn-icon notification-btn" onclick="toggleDropdown('notifDropdown')">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notifBadge" style="display:none;">0</span>
            </button>
            <div id="notifDropdown" class="dropdown-menu notification-dropdown" style="display:none;">
                <div class="dropdown-header">
                    <h4><?= $t('notifications') ?></h4>
                    <button class="btn-link" onclick="markAllRead()"><?= $t('mark_all_read') ?></button>
                </div>
                <div id="notifList" class="notification-list">
                    <div class="empty-state-small"><?= $t('no_notifications') ?></div>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <div class="nav-dropdown">
            <button class="btn-icon user-menu-btn" onclick="toggleDropdown('userDropdown')">
                <div class="user-avatar">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
            </button>
            <div id="userDropdown" class="dropdown-menu user-dropdown" style="display:none;">
                <div class="dropdown-user-info">
                    <div class="user-avatar-lg">
                        <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <strong><?= e($user['name'] ?? '') ?></strong>
                        <small><?= e($user['email'] ?? '') ?></small>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="/settings" class="dropdown-item"><i class="fas fa-cog"></i> <?= $t('settings') ?></a>
                <a href="/users/<?= $user['id'] ?? '' ?>" class="dropdown-item"><i class="fas fa-user"></i> <?= $t('profile') ?></a>
                <div class="dropdown-divider"></div>
                <a href="/logout" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt"></i> <?= $t('logout') ?></a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Layout -->
<div class="app-layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-menu" id="sidebarMenu">
            <!-- Dynamic menu loaded via AJAX -->
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Flash messages -->
        <?php if ($flash = flash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= e($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = flash('error')): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= e($flash) ?></div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script><?php include BASE_PATH . '/public/js/app.js'; ?></script>
<?php if (isset($extraJs)): ?>
<script><?= $extraJs ?></script>
<?php endif; ?>
</body>
</html>
