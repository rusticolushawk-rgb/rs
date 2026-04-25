<?php ob_start(); ?>

<div class="view-toolbar">
    <div class="view-toolbar-left">
        <a href="/users" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> <?= $t('back') ?></a>
        <h2 style="font-size:18px; font-weight:600;">
            <?= $user ? $t('edit_user') : $t('add_user') ?>
        </h2>
    </div>
</div>

<div class="form-view">
    <div class="form-view-body">
        <form method="POST" action="<?= $user ? "/users/{$user['id']}" : '/users' ?>">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $t('name') ?> *</label>
                    <input type="text" name="name" class="form-control" value="<?= e($user['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label><?= $t('email') ?> *</label>
                    <input type="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $t('password') ?> <?= $user ? '' : '*' ?></label>
                    <input type="password" name="password" class="form-control" <?= $user ? '' : 'required' ?>
                           placeholder="<?= $user ? 'Leave blank to keep current' : '' ?>">
                </div>
                <div class="form-group">
                    <label><?= $t('phone') ?></label>
                    <input type="tel" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><?= $t('role') ?></label>
                    <select name="role_id" class="form-control">
                        <?php foreach ($roles ?? [] as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= ($user['role_id'] ?? 3) == $role['id'] ? 'selected' : '' ?>>
                                <?= e($role['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= $t('status') ?></label>
                    <select name="is_active" class="form-control">
                        <option value="1" <?= ($user['is_active'] ?? 1) ? 'selected' : '' ?>><?= $t('active') ?></option>
                        <option value="0" <?= ($user['is_active'] ?? 1) ? '' : 'selected' ?>><?= $t('inactive') ?></option>
                    </select>
                </div>
            </div>

            <div class="mt-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $t('save') ?></button>
                <a href="/users" class="btn btn-secondary"><?= $t('cancel') ?></a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include BASE_PATH . '/views/layouts/main.php';
?>
