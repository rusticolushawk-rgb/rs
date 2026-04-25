<?php
/**
 * Generic Module Page Template
 * Supports Kanban, List, and Form views with search/filter
 *
 * Required variables:
 *  - $moduleTitle: string
 *  - $moduleIcon: string 
 *  - $columns: array of ['key' => 'field', 'label' => 'Label']
 *  - $records: array of records
 *  - $kanbanColumns: array of ['status' => 'Status', 'color' => '#hex'] (optional)
 *  - $kanbanTitleField: string (optional)
 *  - $kanbanMetaFields: array (optional)
 *  - $createUrl: string (optional)
 *  - $formFields: array (optional)
 *  - $smartButtons: array (optional)
 *  - $baseUrl: string
 */
ob_start();
$viewMode = $_GET['view'] ?? 'list';
?>

<div class="view-toolbar">
    <div class="view-toolbar-left">
        <h2 style="font-size:18px; font-weight:600;">
            <i class="<?= e($moduleIcon ?? 'fas fa-cube') ?>"></i> <?= e($moduleTitle ?? 'Module') ?>
        </h2>
        <?php if (!empty($createUrl)): ?>
            <a href="<?= e($createUrl) ?>" class="btn btn-primary"><i class="fas fa-plus"></i> <?= $t('create') ?></a>
        <?php endif; ?>
    </div>
    <div class="view-toolbar-right">
        <div class="view-switcher">
            <button class="view-switcher-btn <?= $viewMode === 'list' ? 'active' : '' ?>" data-view="list" 
                    onclick="window.location.href='?view=list'">
                <i class="fas fa-list"></i>
            </button>
            <button class="view-switcher-btn <?= $viewMode === 'kanban' ? 'active' : '' ?>" data-view="kanban"
                    onclick="window.location.href='?view=kanban'">
                <i class="fas fa-columns"></i>
            </button>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="<?= $t('search') ?>..." 
                   oninput="GyrFalcon.filterTable(this.value, 'moduleTable')">
        </div>
    </div>
</div>

<?php if (!empty($smartButtons)): ?>
<div class="smart-buttons">
    <?php foreach ($smartButtons as $sb): ?>
        <div class="smart-btn">
            <span class="smart-btn-count"><?= e($sb['count'] ?? 0) ?></span>
            <span class="smart-btn-label"><?= e($sb['label'] ?? '') ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($viewMode === 'kanban' && !empty($kanbanColumns)): ?>
<!-- Kanban View -->
<div class="kanban-board">
    <?php foreach ($kanbanColumns as $colStatus => $colConfig): ?>
        <?php
        $colRecords = array_filter($records ?? [], function($r) use ($colStatus, $kanbanStatusField) {
            return ($r[$kanbanStatusField ?? 'status'] ?? '') === $colStatus;
        });
        ?>
        <div class="kanban-column">
            <div class="kanban-column-header" style="border-color:<?= e($colConfig['color'] ?? '#875A7B') ?>">
                <span><?= e($colConfig['label'] ?? $colStatus) ?></span>
                <span class="kanban-column-count"><?= count($colRecords) ?></span>
            </div>
            <div class="kanban-column-body">
                <?php foreach ($colRecords as $rec): ?>
                    <div class="kanban-card" onclick="window.location.href='<?= e($baseUrl) ?>/<?= e($rec['id'] ?? '') ?>'">
                        <div class="kanban-card-title"><?= e($rec[$kanbanTitleField ?? 'name'] ?? '') ?></div>
                        <div class="kanban-card-meta">
                            <?php foreach ($kanbanMetaFields ?? [] as $mf): ?>
                                <span><?= e($rec[$mf] ?? '') ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($rec['amount']) || !empty($rec['total_amount'])): ?>
                            <div class="kanban-card-footer">
                                <span class="kanban-card-amount"><?= formatCurrency((float)($rec['amount'] ?? $rec['total_amount'] ?? 0), $rec['currency'] ?? 'EGP') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($colRecords)): ?>
                    <div class="empty-state-small"><?= $t('no_data') ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<!-- List View -->
<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="table" id="moduleTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php foreach ($columns ?? [] as $col): ?>
                            <th><?= e($col['label'] ?? $col['key']) ?></th>
                        <?php endforeach; ?>
                        <th><?= $t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="<?= count($columns ?? []) + 2 ?>" class="text-center"><?= $t('no_data') ?></td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $rec): ?>
                            <tr>
                                <td><?= e($rec['id'] ?? '') ?></td>
                                <?php foreach ($columns ?? [] as $col): ?>
                                    <td>
                                        <?php
                                        $val = $rec[$col['key']] ?? '';
                                        if (!empty($col['badge'])) {
                                            $badgeClass = $col['badge'][$val] ?? 'badge-secondary';
                                            echo '<span class="badge ' . e($badgeClass) . '">' . e($val) . '</span>';
                                        } elseif (!empty($col['currency'])) {
                                            echo formatCurrency((float)$val, $rec['currency'] ?? 'EGP');
                                        } else {
                                            echo e($val);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="actions">
                                    <a href="<?= e($baseUrl) ?>/<?= e($rec['id'] ?? '') ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
