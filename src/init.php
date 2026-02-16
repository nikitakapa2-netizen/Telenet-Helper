<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

function allStatuses(): array
{
    $rows = db()->query('SELECT id, code, name FROM statuses ORDER BY id')->fetchAll();
    $result = [];

    foreach ($rows as $row) {
        $result[$row['code']] = ['id' => (int) $row['id'], 'name' => $row['name']];
    }

    return $result;
}

function getStatusCodeById(int $statusId): ?string
{
    $stmt = db()->prepare('SELECT code FROM statuses WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $statusId]);
    $row = $stmt->fetch();

    return $row['code'] ?? null;
}

function addHistory(int $requestId, int $statusId, int $userId, ?string $comment = null): void
{
    $stmt = db()->prepare(
        'INSERT INTO request_history (request_id, status_id, changed_by_user, comment) VALUES (:request_id, :status_id, :changed_by_user, :comment)'
    );

    $stmt->execute([
        'request_id' => $requestId,
        'status_id' => $statusId,
        'changed_by_user' => $userId,
        'comment' => $comment,
    ]);
}
