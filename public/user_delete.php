<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';

$admin = requireRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users.php');
}

$userId = (int) ($_POST['id'] ?? 0);
$resetPassword = isset($_POST['reset_password']);

$stmt = db()->prepare('SELECT id, role FROM users WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $userId]);
$target = $stmt->fetch();

if (!$target) {
    flash('error', 'Пользователь не найден.');
    redirect('users.php');
}

if ($resetPassword) {
    $newPassword = 'newpass123';
    $update = db()->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
    $update->execute([
        'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        'id' => $userId,
    ]);

    flash('success', 'Пароль сброшен. Новый пароль: ' . $newPassword);
    redirect('users.php');
}

if ($target['role'] === 'admin') {
    flash('error', 'Нельзя удалить admin.');
    redirect('users.php');
}

$hasRequests = db()->prepare('SELECT id FROM requests WHERE client_id = :id OR assigned_technician_id = :id LIMIT 1');
$hasRequests->execute(['id' => $userId]);

if ($hasRequests->fetch()) {
    flash('error', 'Нельзя удалить пользователя, есть связанные заявки');
    redirect('users.php');
}

$delete = db()->prepare('DELETE FROM users WHERE id = :id');
$delete->execute(['id' => $userId]);

flash('success', 'Пользователь удален.');
redirect('users.php');
