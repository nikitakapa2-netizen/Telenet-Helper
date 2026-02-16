<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireAuth();
$requestId = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT r.*, s.code AS status_code, s.name AS status_name, c.name AS client_name, c.email AS client_email,
            t.name AS technician_name
     FROM requests r
     JOIN statuses s ON s.id = r.status_id
     JOIN users c ON c.id = r.client_id
     LEFT JOIN users t ON t.id = r.assigned_technician_id
     WHERE r.id = :id LIMIT 1'
);
$stmt->execute(['id' => $requestId]);
$request = $stmt->fetch();

if (!$request) {
    flash('error', 'Заявка не найдена.');
    redirect('dashboard.php');
}

if ($user['role'] === 'client' && (int) $request['client_id'] !== (int) $user['id']) {
    flash('error', 'У вас нет доступа к этой заявке.');
    redirect('my_requests.php');
}

if ($user['role'] === 'technician' && (int) $request['assigned_technician_id'] !== (int) $user['id']) {
    flash('error', 'У вас нет доступа к этой заявке.');
    redirect('all_requests.php');
}

$statuses = allStatuses();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['comment'] ?? '');

    if (isset($_POST['operator_update']) && in_array($user['role'], ['operator', 'admin'], true)) {
        $newStatus = $_POST['status_code'] ?? '';
        $technicianId = (int) ($_POST['technician_id'] ?? 0);

        if (isset($statuses[$newStatus])) {
            $assigned = $technicianId > 0 ? $technicianId : null;
            $update = db()->prepare('UPDATE requests SET status_id = :status_id, assigned_technician_id = :assigned WHERE id = :id');
            $update->bindValue(':status_id', $statuses[$newStatus]['id'], PDO::PARAM_INT);
            $update->bindValue(':assigned', $assigned, $assigned === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $update->bindValue(':id', $requestId, PDO::PARAM_INT);
            $update->execute();

            addHistory($requestId, $statuses[$newStatus]['id'], (int) $user['id'], $comment ?: 'Изменение оператором');
            flash('success', 'Заявка обновлена.');
            redirect('request_view.php?id=' . $requestId);
        }
    }

    if (isset($_POST['technician_update']) && $user['role'] === 'technician') {
        $newStatus = $_POST['status_code'] ?? '';
        if (in_array($newStatus, ['in_progress', 'done'], true)) {
            $update = db()->prepare('UPDATE requests SET status_id = :status_id WHERE id = :id');
            $update->execute(['status_id' => $statuses[$newStatus]['id'], 'id' => $requestId]);

            addHistory($requestId, $statuses[$newStatus]['id'], (int) $user['id'], $comment ?: 'Обновление мастером');
            flash('success', 'Статус обновлен.');
            redirect('request_view.php?id=' . $requestId);
        }
    }
}

$techs = db()->query("SELECT id, name FROM users WHERE role = 'technician' ORDER BY name")->fetchAll();
$historyStmt = db()->prepare(
    'SELECT h.created_at, s.name AS status_name, u.name AS changed_by, h.comment
     FROM request_history h
     JOIN statuses s ON s.id = h.status_id
     JOIN users u ON u.id = h.changed_by_user
     WHERE h.request_id = :id
     ORDER BY h.created_at DESC'
);
$historyStmt->execute(['id' => $requestId]);
$historyRows = $historyStmt->fetchAll();

layoutHeader('Заявка #' . $requestId, $user);
?>
<h1 class="h3">Заявка #<?= (int) $requestId ?></h1>
<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5">Информация</h2>
            <ul class="list-unstyled mb-0">
                <li><strong>Клиент:</strong> <?= e($request['client_name']) ?> (<?= e($request['client_email']) ?>)</li>
                <li><strong>Тип:</strong> <?= e(requestTypeOptions()[$request['type']] ?? $request['type']) ?></li>
                <li><strong>Адрес:</strong> <?= e($request['address']) ?></li>
                <li><strong>Телефон:</strong> <?= e($request['phone']) ?></li>
                <li><strong>Статус:</strong> <?= e($request['status_name']) ?></li>
                <li><strong>Мастер:</strong> <?= e($request['technician_name'] ?? 'Не назначен') ?></li>
                <li><strong>Описание:</strong> <?= e($request['description']) ?></li>
            </ul>
        </div></div>
    </div>

    <div class="col-lg-6">
        <?php if (in_array($user['role'], ['operator', 'admin'], true)): ?>
            <div class="card shadow-sm mb-3"><div class="card-body">
                <h2 class="h5">Действия оператора</h2>
                <form method="post" class="vstack gap-2">
                    <input type="hidden" name="operator_update" value="1">
                    <div>
                        <label class="form-label">Статус</label>
                        <select name="status_code" class="form-select">
                            <?php foreach ($statuses as $code => $status): ?>
                                <option value="<?= e($code) ?>" <?= $request['status_code'] === $code ? 'selected' : '' ?>>
                                    <?= e($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Назначить мастера</label>
                        <select name="technician_id" class="form-select">
                            <option value="0">Не назначать</option>
                            <?php foreach ($techs as $tech): ?>
                                <option value="<?= (int) $tech['id'] ?>" <?= (int) $request['assigned_technician_id'] === (int) $tech['id'] ? 'selected' : '' ?>>
                                    <?= e($tech['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Комментарий</label>
                        <textarea name="comment" class="form-control" rows="3"></textarea>
                    </div>
                    <button class="btn btn-primary">Сохранить изменения</button>
                </form>
            </div></div>
        <?php elseif ($user['role'] === 'technician' && (int) $request['assigned_technician_id'] === (int) $user['id']): ?>
            <div class="card shadow-sm mb-3"><div class="card-body">
                <h2 class="h5">Действия мастера</h2>
                <form method="post" class="vstack gap-2">
                    <input type="hidden" name="technician_update" value="1">
                    <div>
                        <label class="form-label">Статус</label>
                        <select name="status_code" class="form-select">
                            <option value="in_progress">В работе</option>
                            <option value="done">Выполнена</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Комментарий</label>
                        <textarea name="comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <button class="btn btn-primary">Обновить статус</button>
                </form>
            </div></div>
        <?php endif; ?>

        <div class="card shadow-sm"><div class="card-body">
            <h2 class="h5">История статусов</h2>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead><tr><th>Дата</th><th>Статус</th><th>Кто изменил</th><th>Комментарий</th></tr></thead>
                    <tbody>
                    <?php foreach ($historyRows as $row): ?>
                        <tr>
                            <td><?= e($row['created_at']) ?></td>
                            <td><?= e($row['status_name']) ?></td>
                            <td><?= e($row['changed_by']) ?></td>
                            <td><?= e($row['comment'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div></div>
    </div>
</div>
<?php
layoutFooter();
