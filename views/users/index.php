<?php ob_start(); ?>

<div class="view-toolbar">
    <div class="view-toolbar-left">
        <h2 style="font-size:18px; font-weight:600;"><i class="fas fa-users"></i> <?= $t('user_management') ?></h2>
        <a href="/users/create" class="btn btn-primary"><i class="fas fa-plus"></i> <?= $t('add_user') ?></a>
    </div>
    <div class="view-toolbar-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="<?= $t('search') ?> <?= $t('users') ?>..." 
                   oninput="GyrFalcon.filterTable(this.value, 'usersTable')">
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-container">
            <table class="table" id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?= $t('name') ?></th>
                        <th><?= $t('email') ?></th>
                        <th><?= $t('role') ?></th>
                        <th><?= $t('status') ?></th>
                        <th><?= $t('last_login') ?></th>
                        <th><?= $t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users ?? [] as $u): ?>
                        <tr>
                            <td><?= e($u['id']) ?></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div class="user-avatar" style="width:28px;height:28px;font-size:11px;"><?= strtoupper(substr($u['name'], 0, 1)) ?></div>
                                    <strong><?= e($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td><span class="badge badge-primary"><?= e($u['role_name'] ?? 'N/A') ?></span></td>
                            <td>
                                <?php if ($u['is_active']): ?>
                                    <span class="badge badge-success"><?= $t('active') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= $t('inactive') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $u['last_login'] ? e($u['last_login']) : '--' ?></td>
                            <td class="actions">
                                <a href="/users/<?= e($u['id']) ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                <?php if ($u['id'] != ($user['id'] ?? 0)): ?>
                                    <form method="POST" action="/users/<?= e($u['id']) ?>/delete" style="display:inline" onsubmit="return confirm('<?= $t('confirm_delete_user') ?>')">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total > $perPage): ?>
            <div class="pagination">
                <button class="page-btn" <?= $page <= 1 ? 'disabled' : '' ?> onclick="window.location.href='?page=<?= $page - 1 ?>'">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="page-info"><?= $t('showing') ?> <?= ($page - 1) * $perPage + 1 ?>-<?= min($page * $perPage, $total) ?> <?= $t('of') ?> <?= $total ?></span>
                <button class="page-btn" <?= $page * $perPage >= $total ? 'disabled' : '' ?> onclick="window.location.href='?page=<?= $page + 1 ?>'">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
