<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireRole(['admin']);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || !in_array($role, ['operator', 'technician', 'client'], true)) {
        $error = 'Заполните все поля корректно.';
    } else {
        $exists = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $exists->execute(['email' => $email]);

        if ($exists->fetch()) {
            $error = 'Email уже используется.';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO users (name, email, phone, password_hash, role) VALUES (:name, :email, :phone, :password_hash, :role)'
            );
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
            ]);

            flash('success', 'Пользователь успешно добавлен.');
            redirect('users.php');
        }
    }
}

layoutHeader('Создание пользователя', $user);
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-body p-4">
                <h1 class="h4 mb-3">Новый пользователь</h1>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="row g-3 mobile-stack">
                    <div class="col-12">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Телефон</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Роль</label>
                        <select class="form-select" name="role" required>
                            <option value="operator">Оператор</option>
                            <option value="technician">Мастер</option>
                            <option value="client">Клиент</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Пароль</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-12"><button class="btn btn-primary w-100">Создать</button></div>
                </form>
            </div></div>
    </div>
</div>
<?php
layoutFooter();
