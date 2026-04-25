<!DOCTYPE html>
<html lang="<?= $locale ?? 'en' ?>" dir="<?= $dir ?? 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t('login') ?> — <?= $t('app_name') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style><?php include BASE_PATH . '/public/css/app.css'; ?></style>
</head>
<body class="<?= ($isRtl ?? false) ? 'rtl' : 'ltr' ?>">
    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <i class="fas fa-feather-alt"></i>
                <h1><?= $t('app_name') ?></h1>
                <p><?= $t('sign_in_to_account') ?></p>
            </div>

            <?php if (!empty($_SESSION['login_error'])): ?>
                <div class="login-error">
                    <i class="fas fa-exclamation-circle"></i> <?= e($_SESSION['login_error']) ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form class="login-form" method="POST" action="/login">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="email"><?= $t('email_address') ?></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="admin@gyrfalcon.com" value="admin@gyrfalcon.com" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password"><?= $t('password') ?></label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="<?= $t('password') ?>" value="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-sign-in-alt"></i> <?= $t('sign_in') ?>
                    </button>
                </div>
            </form>

            <div class="text-center mt-2">
                <button class="btn btn-secondary btn-sm" onclick="switchLoginLang()">
                    <i class="fas fa-globe"></i> <?= ($locale ?? 'en') === 'en' ? 'العربية' : 'English' ?>
                </button>
            </div>
        </div>
    </div>
    <script>
        function switchLoginLang() {
            fetch('/api/lang/switch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ locale: '<?= ($locale ?? 'en') === 'en' ? 'ar' : 'en' ?>' })
            }).then(() => window.location.reload());
        }
    </script>
</body>
</html>
