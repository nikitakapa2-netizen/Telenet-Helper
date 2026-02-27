<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/init.php';
require_once __DIR__ . '/../src/layout.php';

$user = requireRole(['client']);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? '';
    $types = array_keys(requestTypeOptions());

    if ($address === '' || $phone === '' || $description === '' || !in_array($type, $types, true)) {
        $error = 'Заполните все поля корректно.';
    } elseif (!isValidPhone($phone)) {
        $error = 'Некорректный номер телефона. Пример: +7 (999) 123-45-67.';
    } else {
        $statuses = allStatuses();
        $createdId = $statuses['created']['id'];
        $normalizedPhone = normalizePhone($phone);

        if (strlen($normalizedPhone) === 11 && $normalizedPhone[0] === '8') {
            $normalizedPhone = '7' . substr($normalizedPhone, 1);
        }

        $stmt = db()->prepare(
            'INSERT INTO requests (client_id, address, phone, description, type, status_id) VALUES (:client_id, :address, :phone, :description, :type, :status_id)'
        );
        $stmt->execute([
            'client_id' => $user['id'],
            'address' => $address,
            'phone' => '+' . $normalizedPhone,
            'description' => $description,
            'type' => $type,
            'status_id' => $createdId,
        ]);

        $requestId = (int) db()->lastInsertId();
        addHistory($requestId, $createdId, $user['id'], 'Заявка создана клиентом');

        flash('success', 'Заявка успешно создана.');
        redirect('my_requests.php');
    }
}

layoutHeader('Создание заявки', $user);
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Создать заявку</h1>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" class="row g-3 mobile-stack">
                    <div class="col-12">
                        <label class="form-label">Тип заявки</label>
                        <select name="type" class="form-select" required>
                            <option value="">Выберите тип</option>
                            <?php foreach (requestTypeOptions() as $code => $name): ?>
                                <option value="<?= e($code) ?>"><?= e($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Адрес</label>
                        <input type="text" class="form-control" name="address" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Телефон</label>
                        <input type="text" class="form-control" name="phone" placeholder="+7 (999) 123-45-67" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Описание проблемы</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary w-100">Отправить заявку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
layoutFooter();
