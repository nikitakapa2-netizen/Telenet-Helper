<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireRole(['client']);
$success = getFlash('success');

$stmt = db()->prepare(
    'SELECT r.id, r.address, r.phone, r.type, s.name AS status_name, r.created_at
     FROM requests r
     JOIN statuses s ON s.id = r.status_id
     WHERE r.client_id = :client_id
     ORDER BY r.created_at DESC'
);
$stmt->execute(['client_id' => $user['id']]);
$requests = $stmt->fetchAll();

layoutHeader('Мои заявки', $user);
?>
<h1 class="h3 mb-3">Мои заявки</h1>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<div class="table-responsive card shadow-sm">
    <table class="table table-striped mb-0 align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Тип</th>
            <th>Адрес</th>
            <th>Телефон</th>
            <th>Статус</th>
            <th>Дата</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $request): ?>
            <tr>
                <td>#<?= (int) $request['id'] ?></td>
                <td><?= e(requestTypeOptions()[$request['type']] ?? $request['type']) ?></td>
                <td><?= e($request['address']) ?></td>
                <td><?= e($request['phone']) ?></td>
                <td><span class="badge bg-secondary"><?= e($request['status_name']) ?></span></td>
                <td><?= e($request['created_at']) ?></td>
                <td><a href="request_view.php?id=<?= (int) $request['id'] ?>" class="btn btn-sm btn-primary">Открыть</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
layoutFooter();
