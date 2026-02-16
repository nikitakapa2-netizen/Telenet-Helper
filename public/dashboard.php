<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireAuth();
$success = getFlash('success');
$error = getFlash('error');

layoutHeader('Панель управления', $user);
?>
<h1 class="h3 mb-3">Панель управления</h1>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<div class="row g-3">
    <?php if ($user['role'] === 'client'): ?>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Новая заявка</h2>
                <p>Создайте заявку на подключение или ремонт.</p>
                <a href="create_request.php" class="btn btn-primary w-100">Создать заявку</a>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Мои заявки</h2>
                <p>Просмотр статусов и истории изменений.</p>
                <a href="my_requests.php" class="btn btn-outline-primary w-100">Открыть список</a>
            </div></div>
        </div>
    <?php elseif ($user['role'] === 'operator'): ?>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Все заявки</h2>
                <p>Назначение мастеров и изменение статусов.</p>
                <a href="all_requests.php" class="btn btn-primary w-100">Перейти к заявкам</a>
            </div></div>
        </div>
    <?php elseif ($user['role'] === 'technician'): ?>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Назначенные заявки</h2>
                <p>Обновляйте статус работ и добавляйте комментарии.</p>
                <a href="all_requests.php" class="btn btn-primary w-100">Открыть заявки</a>
            </div></div>
        </div>
    <?php elseif ($user['role'] === 'admin'): ?>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Управление пользователями</h2>
                <p>Добавление, удаление пользователей и сброс паролей.</p>
                <a href="users.php" class="btn btn-primary w-100">Пользователи</a>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h5">Все заявки</h2>
                <p>Контроль полного цикла обработки заявок.</p>
                <a href="all_requests.php" class="btn btn-outline-primary w-100">К заявкам</a>
            </div></div>
        </div>
    <?php endif; ?>
</div>
<?php
layoutFooter();
