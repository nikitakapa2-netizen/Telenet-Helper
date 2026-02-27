<?php

declare(strict_types=1);

function layoutHeader(string $title, ?array $user = null): void
{
    ?>
    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="assets/css/app.css" rel="stylesheet">
    </head>
    <body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Telenet Helper</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($user): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Панель</a></li>
                        <?php if ($user['role'] === 'client'): ?>
                            <li class="nav-item"><a class="nav-link" href="create_request.php">Новая заявка</a></li>
                            <li class="nav-item"><a class="nav-link" href="my_requests.php">Мои заявки</a></li>
                        <?php endif; ?>
                        <?php if (in_array($user['role'], ['operator', 'admin', 'technician'], true)): ?>
                            <li class="nav-item"><a class="nav-link" href="all_requests.php">Все заявки</a></li>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="users.php">Пользователи</a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <div class="d-flex gap-2">
                    <?php if ($user): ?>
                        <span class="navbar-text text-white"><?= e($user['name']) ?> (<?= e($user['role']) ?>)</span>
                        <a class="btn btn-outline-light" href="logout.php">Выход</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light" href="login.php">Вход</a>
                        <a class="btn btn-light" href="register.php">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container pb-5">
    <?php
}

function layoutFooter(): void
{
    ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}
