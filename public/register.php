<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

if (currentUser()) {
    redirect('dashboard.php');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Заполните обязательные поля.';
    } else {
        $exists = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $exists->execute(['email' => $email]);

        if ($exists->fetch()) {
            $error = 'Пользователь с таким email уже существует.';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO users (name, email, phone, password_hash, role) VALUES (:name, :email, :phone, :password_hash, :role)'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'client',
            ]);

            flash('success', 'Регистрация завершена. Теперь войдите в систему.');
            redirect('login.php');
        }
    }
}

layoutHeader('Регистрация');
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Регистрация клиента</h1>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="row g-3 mobile-stack">
                    <div class="col-md-6">
                        <label class="form-label">ФИО</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Телефон</label>
                        <input type="text" class="form-control" name="phone" placeholder="+7...">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" minlength="6" required>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100">Зарегистрироваться</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
layoutFooter();
