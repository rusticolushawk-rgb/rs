<?php ob_start(); ?>

<div class="view-toolbar">
    <div class="view-toolbar-left">
        <h2 style="font-size:18px; font-weight:600;"><i class="fas fa-history"></i> <?= $t('audit_logs') ?></h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= $t('user_name') ?></th>
                        <th><?= $t('action') ?></th>
                        <th><?= $t('entity') ?></th>
                        <th><?= $t('description') ?></th>
                        <th><?= $t('ip_address') ?></th>
                        <th><?= $t('timestamp') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs ?? [] as $log): ?>
                        <tr>
                            <td><?= e($log['id']) ?></td>
                            <td><?= e($log['user_name'] ?? 'System') ?></td>
                            <td><span class="badge badge-<?= $log['action'] === 'login' ? 'success' : ($log['action'] === 'delete' ? 'danger' : 'info') ?>"><?= e($log['action']) ?></span></td>
                            <td><?= e($log['entity_type'] ?? '') ?></td>
                            <td><?= e($log['description'] ?? '') ?></td>
                            <td><code><?= e($log['ip_address'] ?? '') ?></code></td>
                            <td><?= e($log['created_at'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
