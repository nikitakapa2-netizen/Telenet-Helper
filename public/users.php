<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireRole(['admin']);
$success = getFlash('success');
$error = getFlash('error');

$users = db()->query('SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

layoutHeader('Пользователи', $user);
?>
<h1 class="h3 mb-3">Пользователи</h1>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="mb-3">
    <a href="user_create.php" class="btn btn-primary">Добавить пользователя</a>
</div>
<div class="table-responsive card shadow-sm">
    <table class="table table-striped mb-0 align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Роль</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $item): ?>
            <tr>
                <td><?= (int) $item['id'] ?></td>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['email']) ?></td>
                <td><?= e($item['phone']) ?></td>
                <td><span class="badge bg-dark"><?= e($item['role']) ?></span></td>
                <td><?= e($item['created_at']) ?></td>
                <td class="d-flex flex-wrap gap-2">
                    <?php if ($item['role'] !== 'admin'): ?>
                        <form method="post" action="user_delete.php" onsubmit="return confirm('Удалить пользователя?');">
                            <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                            <button class="btn btn-sm btn-danger">Удалить</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted small">Удаление запрещено</span>
                    <?php endif; ?>
                    <form method="post" action="user_delete.php">
                        <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                        <input type="hidden" name="reset_password" value="1">
                        <button class="btn btn-sm btn-warning">Сбросить пароль</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
layoutFooter();
