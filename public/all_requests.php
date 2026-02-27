<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireRole(['operator', 'technician', 'admin']);
$success = getFlash('success');
$error = getFlash('error');

$sql = 'SELECT r.id, r.address, r.phone, r.type, s.name AS status_name, c.name AS client_name, c.phone AS client_phone,
               t.name AS technician_name, r.created_at
        FROM requests r
        JOIN statuses s ON s.id = r.status_id
        JOIN users c ON c.id = r.client_id
        LEFT JOIN users t ON t.id = r.assigned_technician_id';
$params = [];

if ($user['role'] === 'technician') {
    $sql .= ' WHERE r.assigned_technician_id = :technician_id';
    $params['technician_id'] = $user['id'];
}

$sql .= ' ORDER BY r.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

layoutHeader('Все заявки', $user);
?>
<h1 class="h3 mb-3">Список заявок</h1>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

<?php if ($user['role'] === 'admin'): ?>
    <form method="post" action="report.php" class="mb-3 d-flex flex-wrap gap-2">
        <input type="hidden" name="scope" value="selected" id="reportScopeField">
        <button type="submit" class="btn btn-success" onclick="document.getElementById('reportScopeField').value='selected'">
            Скачать отчет по выбранным
        </button>
        <button type="submit" class="btn btn-outline-success" onclick="document.getElementById('reportScopeField').value='all'">
            Скачать отчет по всем заявкам
        </button>

        <div class="table-responsive card shadow-sm w-100 mt-2">
            <table class="table table-striped mb-0 align-middle">
                <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Клиент</th>
                    <th>Тип</th>
                    <th>Адрес</th>
                    <th>Статус</th>
                    <th>Мастер</th>
                    <th>Дата</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><input type="checkbox" name="request_ids[]" value="<?= (int) $request['id'] ?>" class="request-check"></td>
                        <td>#<?= (int) $request['id'] ?></td>
                        <td>
                            <?= e($request['client_name']) ?><br>
                            <small class="text-muted"><?= e($request['client_phone']) ?></small>
                        </td>
                        <td><?= e(requestTypeOptions()[$request['type']] ?? $request['type']) ?></td>
                        <td><?= e($request['address']) ?></td>
                        <td><span class="badge bg-secondary"><?= e($request['status_name']) ?></span></td>
                        <td><?= e($request['technician_name'] ?? 'Не назначен') ?></td>
                        <td><?= e($request['created_at']) ?></td>
                        <td><a class="btn btn-sm btn-primary" href="request_view.php?id=<?= (int) $request['id'] ?>">Детали</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <script>
        const checkAll = document.getElementById('checkAll');
        const checks = document.querySelectorAll('.request-check');
        if (checkAll) {
            checkAll.addEventListener('change', () => {
                checks.forEach((item) => {
                    item.checked = checkAll.checked;
                });
            });
        }
    </script>
<?php else: ?>
    <div class="table-responsive card shadow-sm">
        <table class="table table-striped mb-0 align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Тип</th>
                <th>Адрес</th>
                <th>Статус</th>
                <th>Мастер</th>
                <th>Дата</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td>#<?= (int) $request['id'] ?></td>
                    <td>
                        <?= e($request['client_name']) ?><br>
                        <small class="text-muted"><?= e($request['client_phone']) ?></small>
                    </td>
                    <td><?= e(requestTypeOptions()[$request['type']] ?? $request['type']) ?></td>
                    <td><?= e($request['address']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($request['status_name']) ?></span></td>
                    <td><?= e($request['technician_name'] ?? 'Не назначен') ?></td>
                    <td><?= e($request['created_at']) ?></td>
                    <td><a class="btn btn-sm btn-primary" href="request_view.php?id=<?= (int) $request['id'] ?>">Детали</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php
layoutFooter();
