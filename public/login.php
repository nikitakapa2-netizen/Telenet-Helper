<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

if (currentUser()) {
    redirect('dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login($email, $password)) {
        flash('success', 'Вы успешно вошли в систему.');
        redirect('dashboard.php');
    }

    $error = 'Неверный email или пароль.';
}

layoutHeader('Вход');
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Вход</h1>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="vstack gap-3">
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div>
                        <label class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button class="btn btn-primary w-100">Войти</button>
                </form>
                <p class="mt-3 mb-0">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>.</p>
            </div>
        </div>
    </div>
</div>
<?php
layoutFooter();
