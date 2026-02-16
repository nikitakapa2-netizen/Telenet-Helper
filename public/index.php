<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = currentUser();
layoutHeader('Главная', $user);
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 mb-3">Информационная система заявок телеком-компании</h1>
                <p class="lead">Удобная система для регистрации, обработки и контроля заявок на подключение и ремонт.</p>
                <?php if ($user): ?>
                    <a href="dashboard.php" class="btn btn-primary btn-lg">Перейти в личный кабинет</a>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="login.php" class="btn btn-primary btn-lg">Войти</a>
                        <a href="register.php" class="btn btn-outline-primary btn-lg">Регистрация клиента</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
layoutFooter();
