<?php ob_start(); ?>

<h2 style="font-size:18px; font-weight:600; margin-bottom:16px;">
    <i class="fas fa-tachometer-alt"></i> <?= $t('dashboard_overview') ?>
</h2>

<!-- KPI Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--primary)"><i class="fas fa-cubes"></i></div>
        <div class="stat-label"><?= $t('modules') ?></div>
        <div class="stat-value"><?= $stats['total_modules'] ?? 0 ?></div>
        <div class="stat-change positive"><i class="fas fa-arrow-up"></i> <?= $t('active') ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--secondary)"><i class="fas fa-users"></i></div>
        <div class="stat-label"><?= $t('users') ?></div>
        <div class="stat-value"><?= $stats['total_users'] ?? 0 ?></div>
        <div class="stat-change positive"><i class="fas fa-arrow-up"></i> <?= $t('active') ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--warning)"><i class="fas fa-building"></i></div>
        <div class="stat-label"><?= $t('company') ?></div>
        <div class="stat-value"><?= $stats['total_companies'] ?? 0 ?></div>
        <div class="stat-change positive"><i class="fas fa-check"></i> <?= $t('active') ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--info)"><i class="fas fa-chart-line"></i></div>
        <div class="stat-label"><?= $t('total_revenue') ?></div>
        <div class="stat-value" id="revenueValue">--</div>
        <div class="stat-change positive"><i class="fas fa-arrow-up"></i> <?= $t('this_month') ?></div>
    </div>
</div>

<!-- Charts & Activity -->
<div class="dashboard-grid">
    <!-- Revenue Chart -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-area"></i> <?= $t('revenue_chart') ?></h3>
            <div class="btn-group">
                <button class="btn btn-sm btn-secondary"><?= $t('this_month') ?></button>
                <button class="btn btn-sm btn-secondary"><?= $t('this_year') ?></button>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Orders Chart -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> <?= $t('total_orders') ?></h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-history"></i> <?= $t('recent_activities') ?></h3>
            <a href="/audit-logs" class="btn btn-sm btn-secondary"><?= $t('view_all') ?></a>
        </div>
        <div class="card-body">
            <?php if (empty($recentActivity)): ?>
                <div class="empty-state-small"><?= $t('no_data') ?></div>
            <?php else: ?>
                <?php foreach (array_slice($recentActivity, 0, 8) as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background:#E8DEE5; color:var(--primary);">
                            <i class="fas fa-<?= $activity['action'] === 'login' ? 'sign-in-alt' : ($activity['action'] === 'create' ? 'plus' : ($activity['action'] === 'update' ? 'edit' : 'circle')) ?>"></i>
                        </div>
                        <div class="activity-content">
                            <strong><?= e($activity['user_name'] ?? 'System') ?></strong>
                            <p><?= e($activity['description'] ?? $activity['action']) ?></p>
                            <time><?= e($activity['created_at'] ?? '') ?></time>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bolt"></i> <?= $t('quick_actions') ?></h3>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:8px;">
                <a href="/modules" class="btn btn-secondary"><i class="fas fa-puzzle-piece"></i> <?= $t('modules') ?></a>
                <a href="/users" class="btn btn-secondary"><i class="fas fa-users"></i> <?= $t('users') ?></a>
                <a href="/settings" class="btn btn-secondary"><i class="fas fa-cog"></i> <?= $t('settings') ?></a>
                <a href="/audit-logs" class="btn btn-secondary"><i class="fas fa-history"></i> <?= $t('audit_logs') ?></a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
