<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';

$user = requireRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('error', 'Отчет можно скачать только из формы.');
    redirect('all_requests.php');
}

$scope = $_POST['scope'] ?? 'selected';
$ids = $_POST['request_ids'] ?? [];

$sql = 'SELECT r.id, r.address, r.phone, r.description, r.type, r.created_at,
               s.name AS status_name,
               c.name AS client_name,
               t.name AS technician_name
        FROM requests r
        JOIN statuses s ON s.id = r.status_id
        JOIN users c ON c.id = r.client_id
        LEFT JOIN users t ON t.id = r.assigned_technician_id';
$params = [];

if ($scope === 'selected') {
    $ids = array_values(array_filter(array_map('intval', (array) $ids), static fn (int $id): bool => $id > 0));

    if ($ids === []) {
        flash('error', 'Выберите хотя бы одну заявку для выгрузки.');
        redirect('all_requests.php');
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql .= " WHERE r.id IN ({$placeholders})";
    $params = $ids;
}

$sql .= ' ORDER BY r.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="requests_report_' . date('Ymd_His') . '.csv"');

echo "\xEF\xBB\xBF";
$out = fopen('php://output', 'wb');

fputcsv($out, ['ID', 'Тип', 'Статус', 'Клиент', 'Телефон', 'Адрес', 'Мастер', 'Описание', 'Создана']);

$types = requestTypeOptions();

foreach ($rows as $row) {
    fputcsv($out, [
        $row['id'],
        $types[$row['type']] ?? $row['type'],
        $row['status_name'],
        $row['client_name'],
        $row['phone'],
        $row['address'],
        $row['technician_name'] ?? 'Не назначен',
        $row['description'],
        $row['created_at'],
    ]);
}

fclose($out);
exit;
