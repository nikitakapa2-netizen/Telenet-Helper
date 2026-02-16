<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';

logout();
flash('success', 'Вы вышли из системы.');
redirect('login.php');
