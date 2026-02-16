<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function statusOptions(): array
{
    return [
        'created' => 'Создана',
        'accepted' => 'Принята',
        'assigned' => 'Назначена',
        'in_progress' => 'В работе',
        'done' => 'Выполнена',
        'closed' => 'Закрыта',
    ];
}

function requestTypeOptions(): array
{
    return [
        'connect' => 'Подключение',
        'repair' => 'Ремонт',
    ];
}
