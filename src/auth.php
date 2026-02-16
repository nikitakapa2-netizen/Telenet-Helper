<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const ROLES = ['client', 'operator', 'technician', 'admin'];

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);

    $user = $stmt->fetch();

    if (!$user) {
        logout();
        return null;
    }

    return $user;
}

function login(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function requireAuth(): array
{
    $user = currentUser();

    if (!$user) {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function requireRole(array $roles): array
{
    $user = requireAuth();

    if (!in_array($user['role'], $roles, true)) {
        $_SESSION['error'] = 'Недостаточно прав для доступа к странице.';
        header('Location: dashboard.php');
        exit;
    }

    return $user;
}
